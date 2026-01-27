<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    //
    public function index()
    {
        $now = now();
        $last7Days = collect(range(0, 6))->map(fn($i) => $now->copy()->subDays($i)->format('Y-m-d'))->reverse();

        $revenueTrends = \App\Models\Transaction::where('status', \App\Enums\TransactionStatus::Completed)
            ->where('created_at', '>=', $now->copy()->subDays(7))
            ->selectRaw('DATE(created_at) as date, SUM(total_sell_price) as total')
            ->groupBy('date')
            ->pluck('total', 'date');

        $chartData = $last7Days->map(fn($date) => $revenueTrends[$date] ?? 0)->values();
        $chartLabels = $last7Days->map(fn($date) => date('D', strtotime($date)))->values();

        $data = [
            'total_products' => \App\Models\Product::where('active', true)->count(),
            'total_orders' => \App\Models\Transaction::count(),
            'total_sales' => \App\Models\Transaction::where('status', \App\Enums\TransactionStatus::Completed)->sum('total_sell_price'),
            'total_users' => \App\Models\User::where('role', 'customer')->count(),
            'recent_orders' => \App\Models\Transaction::with('user')->latest()->take(6)->get(),
            'top_products' => \App\Models\TransactionSaleLine::select('product_variant_id')
                ->selectRaw('SUM(qty) as total_qty, SUM(qty * price) as total_revenue')
                ->with('variant.product')
                ->groupBy('product_variant_id')
                ->orderByDesc('total_qty')
                ->take(5)
                ->get(),
            'revenue_chart' => [
                'labels' => $chartLabels,
                'data' => $chartData
            ]
        ];

        return view('home.index', compact('data'));
    }
}