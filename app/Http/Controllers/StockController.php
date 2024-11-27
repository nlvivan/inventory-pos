<?php

namespace App\Http\Controllers;

use App\Http\Resources\StockResource;
use App\Models\Stock;
use Illuminate\Http\Request;
use Inertia\Inertia;

class StockController extends Controller
{
    public function index(Request $request)
    {
        $request->mergeIfMissing([
            'per_page' => 15,
        ]);

        $products = Stock::query()
            ->with(['product'])
            ->search($request->search)
            ->paginate($request->per_page);

        return Inertia::render('Admin/Stocks', [
            'records' => StockResource::collection($products),
            'filters' => $request->only('search'),
        ]);
    }

    public function addStock(Request $request, Stock $stock)
    {
        $request->validate([
            'stock' => ['required', 'numeric', 'gt:1'],
        ]);

        $stock->update([
            'stock' => $stock->stock + $request->stock,
        ]);

        return redirect()->back();
    }

    public function store(Request $request)
    {
        //
    }

    public function update(Request $request, Stock $stock)
    {
        $data = $request->validate([
            'stock' => ['required', 'numeric', 'gt:0'],
            'critical_stock' => ['required', 'numeric',  'gt:1'],
        ]);

        $stock->update($data);

        return redirect()->back();
    }

    public function destroy(Request $request)
    {
        //
    }
}
