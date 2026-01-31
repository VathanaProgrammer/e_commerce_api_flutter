<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    public function dashboard(Request $request)
    {
        $period = $request->period ?? 'month'; // day, week, month, year
        
        $startDate = match($period) {
            'day' => Carbon::today(),
            'week' => Carbon::now()->startOfWeek(),
            'month' => Carbon::now()->startOfMonth(),
            'year' => Carbon::now()->startOfYear(),
            default => Carbon::now()->startOfMonth()
        };

        // Total Revenue
        $totalRevenue = Transaction::where('status', 'completed')
            ->where('created_at', '>=', $startDate)
            ->sum('total');

        // Total Orders
        $totalOrders = Transaction::where('created_at', '>=', $startDate)->count();

        // Total Customers
        $totalCustomers = User::where('created_at', '>=', $startDate)->count();

        // Average Order Value
        $avgOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;

        // Revenue Trend
        $revenueTrend = Transaction::where('status', 'completed')
            ->where('created_at', '>=', $startDate)
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total) as revenue'),
                DB::raw('COUNT(*) as orders')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Top Selling Products
        $topProducts = DB::table('transaction_sale_lines')
            ->join('product_variants', 'transaction_sale_lines.product_variant_id', '=', 'product_variants.id')
            ->join('products', 'product_variants.product_id', '=', 'products.id')
            ->join('transactions', 'transaction_sale_lines.transaction_id', '=', 'transactions.id')
            ->where('transactions.created_at', '>=', $startDate)
            ->where('transactions.status', 'completed')
            ->select(
                'products.id',
                'products.name',
                'products.image_url',
                DB::raw('SUM(transaction_sale_lines.quantity) as total_sold'),
                DB::raw('SUM(transaction_sale_lines.quantity * transaction_sale_lines.price) as total_revenue')
            )
            ->groupBy('products.id', 'products.name', 'products.image_url')
            ->orderBy('total_sold', 'desc')
            ->limit(10)
            ->get();

        // Order Status Distribution
        $ordersByStatus = Transaction::where('created_at', '>=', $startDate)
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get();

        // Recent Orders
        $recentOrders = Transaction::with(['user'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'summary' => [
                    'total_revenue' => round($totalRevenue, 2),
                    'total_orders' => $totalOrders,
                    'total_customers' => $totalCustomers,
                    'avg_order_value' => round($avgOrderValue, 2)
                ],
                'revenue_trend' => $revenueTrend,
                'top_products' => $topProducts,
                'orders_by_status' => $ordersByStatus,
                'recent_orders' => $recentOrders,
                'period' => $period,
                'start_date' => $startDate->toDateString()
            ]
        ]);
    }

    public function salesReport(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date'
        ]);

        $sales = Transaction::where('status', 'completed')
            ->whereBetween('created_at', [$request->start_date, $request->end_date])
            ->with(['user', 'saleLines.productVariant.product'])
            ->get();

        $totalRevenue = $sales->sum('total');
        $totalOrders = $sales->count();
        $totalDiscount = $sales->sum('discount_amount');

        return response()->json([
            'success' => true,
            'data' => [
                'sales' => $sales,
                'summary' => [
                    'total_revenue' => round($totalRevenue, 2),
                    'total_orders' => $totalOrders,
                    'total_discount' => round($totalDiscount, 2),
                    'net_revenue' => round($totalRevenue - $totalDiscount, 2)
                ],
                'period' => [
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date
                ]
            ]
        ]);
    }

    public function productPerformance($productId)
    {
        $product = Product::with(['variants', 'reviews'])->findOrFail($productId);

        $totalSold = DB::table('transaction_sale_lines')
            ->join('product_variants', 'transaction_sale_lines.product_variant_id', '=', 'product_variants.id')
            ->join('transactions', 'transaction_sale_lines.transaction_id', '=', 'transactions.id')
            ->where('product_variants.product_id', $productId)
            ->where('transactions.status', 'completed')
            ->sum('transaction_sale_lines.quantity');

        $totalRevenue = DB::table('transaction_sale_lines')
            ->join('product_variants', 'transaction_sale_lines.product_variant_id', '=', 'product_variants.id')
            ->join('transactions', 'transaction_sale_lines.transaction_id', '=', 'transactions.id')
            ->where('product_variants.product_id', $productId)
            ->where('transactions.status', 'completed')
            ->sum(DB::raw('transaction_sale_lines.quantity * transaction_sale_lines.price'));

        $avgRating = $product->reviews->avg('rating');
        $reviewCount = $product->reviews->count();
        $favoriteCount = $product->favoritedBy()->count();

        return response()->json([
            'success' => true,
            'data' => [
                'product' => $product,
                'performance' => [
                    'total_sold' => $totalSold,
                    'total_revenue' => round($totalRevenue, 2),
                    'avg_rating' => round($avgRating, 2),
                    'review_count' => $reviewCount,
                    'favorite_count' => $favoriteCount
                ]
            ]
        ]);
    }
}
