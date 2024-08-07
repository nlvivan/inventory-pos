<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductResource;
use App\Models\Order;
use App\Models\OrderItems;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $data = [];
        if ($request->search) {
            $products = Product::query()
                ->with(['stock', 'category'])
                ->search($request->search)
                ->get();

            $data = ProductResource::collection($products);
        }

        return Inertia::render('Cashier/Cashier', [
            'records' => $data,
            'filters' => $request->only('search'),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'cash' => ['required', 'numeric'],
            'orderItems' => ['required', 'array'],
            'orderItems.*.id' => ['required', 'exists:products,id'],
            'orderItems.*.quantity' => ['required', 'numeric', 'min:1'],
        ]);

        foreach ($data['orderItems'] as $index => $item) {
            $product = Product::find($item['id']);

            if ($product->stock?->stock < $item['quantity']) {
                abort(422, "{$product->name} is not enough stock");
            }
        }

        $totalAmount = collect($data['orderItems'])->sum(function ($item) {
            $product = Product::find($item['id']);

            return $product->price * $item['quantity'];
        });

        if ($totalAmount > $data['cash']) {
            abort(422, 'Cash should be more than the total amount purchased');
        }

        $order = Order::create([
            'total_amount' => $totalAmount,
            'status' => 'pending',
            'employee_id' => auth()->user()->id,
            'cash_amount' => $data['cash'],
            'change_amount' => $data['cash'] - $totalAmount,
            'order_number' => Str::random(8),
        ]);

        foreach ($data['orderItems'] as $orderItem) {
            $product = Product::find($orderItem['id']);

            OrderItems::create([
                'order_id' => $order->id,
                'product_id' => $orderItem['id'],
                'quantity' => $orderItem['quantity'],
                'total_price' => $product->price * $orderItem['quantity'],
            ]);

            $product->stock->update(
                ['stock' => $product->stock->stock - $orderItem['quantity']]
            );
        }

        $order->status = 'paid';
        $order->save();

        return redirect()->back();
    }

    public function show(Order $order)
    {
        $record = $order->load([
            'orderItems',
            'orderItems.product',
            'orderItems.product.category',
            'employee',
            'customer',
        ]);

        return Inertia::render('Cashier/Order', [
            'record' => $record,
        ]);
    }

    public function invoice(Order $order)
    {
        $record = $order->load([
            'orderItems',
            'orderItems.product',
            'orderItems.product.category',
            'employee',
            'customer',
        ]);

        return Inertia::render('Cashier/Invoice', [
            'record' => $record,
        ]);
    }

    public function home(Request $request)
    {
        $records = Order::query()
            ->with([
                'orderItems',
                'orderItems.product',
                'orderItems.product.category',
                'employee',
                'customer',
            ])
            ->search($request->search)
            ->latest()
            ->paginate();

        return Inertia::render('Cashier/Home', [
            'records' => $records,
            'filters' => $request->only('search'),
        ]);
    }

    public function payOrder(Request $request, Order $order)
    {

        $request->validate([
            'cash' => 'required|numeric|gte:'.$order->total_amount,
        ]);

        $order->update([
            'cash_amount' => $request->cash,
            'status' => 'paid',
            'change_amount' => $request->cash - $order->total_amount,
            'employee_id' => auth()->user()->id,
        ]);

        return redirect()->back();
    }
}
