<?php

namespace App\Http\Controllers;

use App\Http\Resources\CartResource;
use App\Models\Notification;
use App\Models\Order;
use App\Models\OrderItems;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use Inertia\Inertia;

class CheckoutController extends Controller
{
    public function index(Request $request)
    {

        $carts = auth()->user()->carts()->with('product')->get();

        return Inertia::render('Checkout/Index', [
            'records' => CartResource::collection($carts),
        ]);
    }

    public function placeOrder(Request $request)
    {
        $request->validate([
            'selectedSchedule' => ['required', 'string'],
        ]);

        $carts = auth()->user()->carts()->get();

        foreach ($carts as $index => $item) {
            $product = Product::find($item['product_id']);

            if ($product->stock?->stock < $item['quantity']) {
                abort(422, "{$product->name} is not enough stock");
            }
        }

        $totalAmount = collect($carts)->sum(function ($item) {
            $product = Product::find($item['product_id']);

            return $product->price * $item['quantity'];
        });

        $order = Order::create([
            'total_amount' => $totalAmount,
            'status' => 'for-pick-up',
            'customer_id' => auth()->user()->id,
            'order_number' => Str::random(8),
            'selected_schedule' => $request->selectedSchedule,
        ]);

        foreach ($carts as $orderItem) {
            $product = Product::find($orderItem['product_id']);

            OrderItems::create([
                'order_id' => $order->id,
                'product_id' => $orderItem['product_id'],
                'quantity' => $orderItem['quantity'],
                'total_price' => $product->price * $orderItem['quantity'],
            ]);

            $product->stock->update(
                ['stock' => $product->stock->stock - $orderItem['quantity']]
            );
        }

        // Artisan::call('app:notify-user-product-low-stock');

        User::role(['admin', 'cashier'])->each(function (User $user) use ($order) {

            Notification::create([
                'type' => 'new_order',
                'user_id' => $user->id,
                'data' => [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'customer_name' => auth()->user()->name,
                    'customer_email' => auth()->user()->email,
                ],
                'read' => false,
            ]);
        });

        auth()->user()->carts()->delete();

        // redirect to success page
        return redirect()->route('checkout.successPage');
    }

    public function successPage()
    {
        return Inertia::render('Checkout/Progress');
    }
}
