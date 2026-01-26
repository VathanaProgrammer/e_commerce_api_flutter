<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    //
    public function index()
    {
        $data = [
            'total_products' => \App\Models\Product::where('active', true)->count(),
            'total_orders' => \App\Models\Transaction::count(),
            'total_sales' => \App\Models\Transaction::where('status', \App\Enums\TransactionStatus::Completed)->sum('total_sell_price'),
            'total_users' => \App\Models\User::where('role', 'customer')->count(),
            'recent_orders' => \App\Models\Transaction::with('user')->latest()->take(5)->get(),
        ];
        return view('home.index', compact('data'));
    }
}