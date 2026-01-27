<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Carbon\Carbon;

class NotificationController extends Controller
{
    /**
     * Check for new orders created since the given timestamp.
     */
    public function checkNewOrders(Request $request)
    {
        $lastTimestamp = $request->get('since');
        
        $query = Transaction::with('user')
            ->orderBy('created_at', 'desc');

        if ($lastTimestamp) {
            $query->where('created_at', '>', Carbon::parse($lastTimestamp));
        } else {
            // If no timestamp, just return the 5 most recent
            return response()->json([
                'new_orders' => [],
                'latest_orders' => $query->limit(5)->get()->map(function($order) {
                    return $this->formatOrder($order);
                }),
                'timestamp' => now()->toDateTimeString()
            ]);
        }

        $newOrders = $query->get();

        return response()->json([
            'new_orders' => $newOrders->map(function($order) {
                return $this->formatOrder($order);
            }),
            'count' => $newOrders->count(),
            'timestamp' => now()->toDateTimeString()
        ]);
    }

    private function formatOrder($order) 
    {
        return [
            'id' => $order->id,
            'invoice_no' => $order->invoice_no,
            'customer' => $order->user ? $order->user->full_name : 'Guest',
            'amount' => '$' . number_format($order->total_sell_price, 2),
            'time' => $order->created_at->diffForHumans(),
            'url' => route('sales.orders') // Link to the orders list
        ];
    }
}
