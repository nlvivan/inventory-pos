<?php

namespace App\Http\Controllers;

use App\Models\ProductReturn;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductReturnController extends Controller
{
    public function index(Request $request)
    {
        $productReturns = ProductReturn::query()
            ->with(['products'])
            ->search($request->search)
            ->paginate();

        return Inertia::render('Admin/ProductReturns', [
            'records' => $productReturns,
            'filters' => $request->only(['search']),
        ]);
    }
}
