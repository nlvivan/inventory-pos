<?php

namespace App\Http\Controllers;

use App\Http\Resources\CartResource;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CartController extends Controller
{
    public function index(Request $request)
    {

        $carts = auth()->user()->carts()->with('product')->get();

        return Inertia::render('Cart/Index', [
            'records' => CartResource::collection($carts),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'gte:1'],
        ]);

        $product = Product::find($data['product_id']);

        if ($product->stock?->stock < $data['quantity']) {
            abort(422, "{$product->name} is not enough stock");
        }

        $user = auth()->user();

        Cart::create([
            'product_id' => $data['product_id'],
            'quantity' => $data['quantity'],
            'user_id' => $user->id,
        ]);

        return redirect()->back();

    }

    public function updateCarts(Request $request)
    {
        $data = $request->validate([
            'records' => ['nullable', 'array'],
            'records.*.id' => ['required', 'integer', 'exists:carts,id'],
            'records.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'records.*.quantity' => ['required', 'integer', 'gte:1'],
        ]);

        foreach ($data['records'] as $index => $item) {
            $product = Product::find($item['product_id']);

            if ($product->stock?->stock < $item['quantity']) {
                abort(422, "{$product->name} is not enough stock");
            }
        }

        foreach ($data['records'] as $record) {
            Cart::find($record['id'])->update([
                'product_id' => $record['product_id'],
                'quantity' => $record['quantity'],
            ]);
        }

        return redirect()->back();
    }

    public function destroy(Cart $cart)
    {
        $cart->delete();

        return redirect()->back();
    }
}
