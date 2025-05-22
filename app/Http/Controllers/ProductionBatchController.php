<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductionBatch;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class ProductionBatchController extends Controller
{
    public function index(Request $request, Product $product)
    {
        $records = $product
            ->productionBatches()
            ->with('stock')
            ->latest()
            ->paginate();

        return Inertia::render('Admin/ProductionBatch', [
            'product' => $product,
            'records' => $records,
            'filters' => $request->only('search'),
        ]);
    }

    public function store(Request $request, Product $product)
    {
        $data = $request->validate([
            'batch_number' => 'required|unique:production_batches,batch_number',
            'production_date' => 'required|date',
            'expiration_date' => 'required|date',
            'stock' => 'required|numeric',
        ]);

        $productionBatch = $product->productionBatches()->create(Arr::except($data, 'stock'));

        $product->stocks()->create([
            'stock' => $data['stock'],
            'production_batch_id' => $productionBatch->id,
        ]);

        return redirect()->back();
    }

    public function update(Request $request, Product $product, ProductionBatch $productionBatch)
    {

        $data = $request->validate([
            'batch_number' => ['required', Rule::unique('production_batches', 'batch_number')->ignore($productionBatch->id)],
            'production_date' => ['required', 'date'],
            'expiration_date' => ['required', 'date'],
            'stock' => 'required|numeric',
        ]);

        $productionBatch->update(Arr::except($data, 'stock'));

        $productionBatch->stock()->update(['stock' => $data['stock']]);

        return redirect()->back();
    }

    public function destroy(Product $product, ProductionBatch $productionBatch)
    {
        $productionBatch->delete();

        $productionBatch->stock()->delete();

        return redirect()->back();
    }

    public function addStock(Request $request, Product $product, ProductionBatch $productionBatch, Stock $stock)
    {
        $data = $request->validate([
            'stock' => ['required', 'numeric', 'gt:0'],
        ]);

        $stock->stock += data_get($data, 'stock');
        $stock->save();

        return redirect()->back();
    }
}
