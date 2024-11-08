<?php

namespace App\Http\Controllers;

use App\Http\Resources\TopSalesResource;
use App\Models\Order;
use App\Models\OrderItems;
use App\Models\Product;
use App\Models\Stock;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminDashboardController extends Controller
{
    public function dashboard(Request $request)
    {

        $totalSales = Order::query()
            ->filterDate($request->top_sales_filter ?? 'today')
            ->where('status', 'paid')
            ->sum('total_amount');

        $topSales = OrderItems::query()
            ->whereHas('order', function ($query) {
                $query->where('status', 'paid');
            })
            ->filterDate($request->top_sales_filter ?? 'this_month')
            ->with('product')
            ->selectRaw('SUM(quantity) as total_quantity, SUM(total_price) as total_price,  product_id')
            ->groupBy('product_id')
            ->orderBy('total_quantity', 'desc')
            ->limit(5)
            ->get();

        $startOfMonth = date('Y-m-01');
        $endOfMonth = date('Y-m-t');

        $nearlyExpiredProducts = Product::whereBetween('expiry_date', [$startOfMonth, $endOfMonth])->get();

        $productNearlyOutOfStock = Stock::query()
            ->with('product')
            ->whereNotNull('stock')
            ->whereNotNull('critical_stock')
            ->whereColumn('stock', '<', 'critical_stock')
            ->get();

        return Inertia::render('Dashboard', [
            'totalSales' => $totalSales,
            'topSales' => TopSalesResource::collection($topSales),
            'filters' => $request->only(['top_sales_filter', 'sales_filter']),
            'nearlyExpiredProducts' => $nearlyExpiredProducts,
            'productNearlyOutOfStock' => $productNearlyOutOfStock,
        ]);
    }

    public function getSalesData(Request $request)
    {
        $date = now();
        $year = $date->year;

        $totalAmount = Order::query()
            ->when($request->input('filter'), function ($query, $filter) use ($year) {
                $query->when($filter == 'last_30', function ($query) use ($year) {
                    $query
                        ->where('status', 'paid')
                        ->whereYear('created_at', $year)
                        ->where('created_at', '>=', now()->subDays(29))
                        ->where('created_at', '<=', now())
                        ->selectRaw("DATE_FORMAT(created_at, '%c/%e/%Y') as date, SUM(total_amount) as total")
                        ->selectRaw('(SELECT CONCAT("order")) as order_type')
                        ->groupBy('date');
                })->when($filter == 'last_7', function ($query) use ($year) {
                    $query
                        ->where('status', 'paid')
                        ->whereYear('created_at', $year)
                        ->where('created_at', '>=', now()->subDays(6))
                        ->where('created_at', '<=', now())
                        ->selectRaw("DATE_FORMAT(created_at, '%m-%d-%Y') as date, SUM(total_amount) as total")
                        ->selectRaw('(SELECT CONCAT("order")) as order_type')
                        ->groupBy('date');
                })->when($filter == 'last_year', function ($query) use ($year) {
                    $query
                        ->where('status', 'paid')
                        ->whereYear('created_at', $year)
                        ->selectRaw('MONTH(created_at) as month, SUM(total_amount) as total')
                        ->selectRaw('(SELECT CONCAT("order")) as order_type')
                        ->groupBy('month');
                });
            })
            ->get();

        return $totalAmount;
    }
}
