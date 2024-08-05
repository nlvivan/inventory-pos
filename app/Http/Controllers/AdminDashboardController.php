<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminDashboardController extends Controller
{
    public function dashboard()
    {
        $totalSalesThisDay = Order::where('created_at', '>=', now()->startOfDay())->where('created_at', '<=', now()->endOfDay())->where('status', 'paid')->sum('total_amount');
        $totalSalesThisMonth = Order::where('created_at', '>=', now()->startOfMonth())->where('created_at', '<=', now()->endOfMonth())->where('status', 'paid')->sum('total_amount');
        $totalSalesThisWeek = Order::where('created_at', '>=', now()->startOfWeek())->where('created_at', '<=', now()->endOfWeek())->where('status', 'paid')->sum('total_amount');
        $totalSalesThisYear = Order::where('created_at', '>=', now()->startOfYear())->where('created_at', '<=', now()->endOfYear())->where('status', 'paid')->sum('total_amount');

        return Inertia::render('Dashboard', [
            'totalSalesThisDay' => $totalSalesThisDay,
            'totalSalesThisMonth' => $totalSalesThisMonth,
            'totalSalesThisWeek' => $totalSalesThisWeek,
            'totalSalesThisYear' => $totalSalesThisYear,
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
                        ->whereYear('created_at', $year)
                        ->where('created_at', '>=', now()->subDays(29))
                        ->where('created_at', '<=', now())
                        ->selectRaw("DATE_FORMAT(created_at, '%c/%e/%Y') as date, SUM(total_amount) as total")
                        ->selectRaw('(SELECT CONCAT("order")) as order_type')
                        ->groupBy('date');
                })->when($filter == 'last_7', function ($query) use ($year) {
                    $query
                        ->whereYear('created_at', $year)
                        ->where('created_at', '>=', now()->subDays(6))
                        ->where('created_at', '<=', now())
                        ->selectRaw("DATE_FORMAT(created_at, '%m-%d-%Y') as date, SUM(total_amount) as total")
                        ->selectRaw('(SELECT CONCAT("order")) as order_type')
                        ->groupBy('date');
                })->when($filter == 'last_year', function ($query) use ($year) {
                    $query
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
