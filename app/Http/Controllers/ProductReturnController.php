<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductionBatch;
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
            ->with(['product'])
            ->search($request->search)
            ->paginate($request->per_page);

        $products = Product::query()
            ->with(['productionBatches'])
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
            'production_batch_id' => 'required|exists:production_batches,id',
            'count' => ['required',
                'integer',
                function ($attribute, $value, $fail) use ($request) {
                    $product = Product::find($request->product_id);
                    $productionBatch = ProductionBatch::find($request->production_batch_id);

                    if ($product && $productionBatch) {
                        $stock = Stock::query()->where('product_id', $product->id)->where('production_batch_id', $productionBatch->id)->first();

                        if ($stock && $value > $stock->stock) {
                            $fail('The count must be less than or equal to the product stock.');
                        }
                    }
                }],
            'reason' => 'nullable|max:255',
        ]);

        ProductReturn::create([
            'product_id' => $request->product_id,
            'production_batch_id' => $request->production_batch_id,
            'count' => $request->count,
            'reason' => $request->reason,
        ]);

        $productStock = Stock::query()
            ->where('product_id', $request->product_id)
            ->where('production_batch_id', $request->production_batch_id)
            ->first();

        if ($productStock) {
            $productStock->stock -= $request->count;
            $productStock->save();
        }

        return redirect()->back();
    }
}
