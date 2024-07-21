<?php

namespace App\Http\Controllers;

use App\Models\ProductionBatch;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class ProductionBatchController extends Controller
{
    public function index(Request $request)
    {
        $records = ProductionBatch::query()
            ->search($request->search)
            ->latest()
            ->paginate();

        return Inertia::render('ProductionBatch/Index', [
            'records' => $records,
            'filters' => $request->only('search'),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'batch_number' => 'required|unique:production_batches,batch_number',
            'production_date' => 'required|date',
        ]);

        ProductionBatch::create($data);

        return redirect()->back();
    }

    public function update(Request $request, ProductionBatch $productionBatch)
    {
        $data = $request->validate([
            'batch_number' => ['required', Rule::unique('production_batches', 'batch_number')->ignore($productionBatch->id)],
            'production_date' => ['required', 'date'],
        ]);

        $productionBatch->update($data);

        return redirect()->back();
    }

    public function destroy(ProductionBatch $productionBatch)
    {
        $productionBatch->delete();

        return redirect()->back();
    }
}
