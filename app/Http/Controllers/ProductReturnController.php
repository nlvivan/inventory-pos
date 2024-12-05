<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductReturn;
use App\Models\Stock;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductReturnController extends Controller
{
    public function index(Request $request)
    {
        $request->mergeIfMissing([
            'per_page' => 15,
        ]);

        $productReturns = ProductReturn::query()
            ->with(['product', 'product.productionBatch'])
            ->search($request->search)
            ->paginate($request->per_page);

        $products = Product::query()
            ->with('productionBatch')
            ->get();

        return Inertia::render('Admin/ProductReturns', [
            'products' => $products,
            'records' => $productReturns,
            'filters' => $request->only(['search']),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'count' => ['required',
                'integer',
                function ($attribute, $value, $fail) use ($request) {
                    $product = Product::find($request->product_id);

                    if ($product && $value > $product->stock->stock) {
                        $fail('The count must be less than or equal to the product stock.');
                    }
                }],
            'reason' => 'nullable|max:255',
        ]);

        ProductReturn::create([
            'product_id' => $request->product_id,
            'count' => $request->count,
            'reason' => $request->reason,
        ]);

        $productStock = Stock::query()->where('product_id', $request->product_id)->first();

        if ($productStock) {
            $productStock->stock -= $request->count;
            $productStock->save();
        }

        return redirect()->back();
    }
}
