<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;

class UserOrdersController extends Controller
{
    /**
     * Get all orders for the authenticated user
     */
    public function index(Request $request)
    {
        $userId = $request->user_id ?? Auth::id();
        
        if (!$userId) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }

        $orders = Transaction::with([
            'saleLines.variant.product',
            'saleLines.variant.attributeValues.attribute',
            'payments'
        ])
        ->where('user_id', $userId)
        ->orderBy('created_at', 'desc')
        ->get()
        ->map(function ($order) {
            return [
                'id' => $order->id,
                'invoice_no' => $order->invoice_no,
                'total_amount' => $order->total_sell_price,
                'total_items' => $order->total_items,
                'status' => $order->status,
                'shipping_status' => $order->shipping_status,
                'shipping_address' => $order->shipping_address,
                'discount_amount' => $order->discount_amount,
                'shipping_charge' => $order->shipping_charge ?? 0,
                'created_at' => $order->created_at->format('Y-m-d H:i:s'),
                'formatted_date' => $order->created_at->format('M d, Y'),
                
                // Payment info
                'payment_method' => $order->payments->first()?->method ?? 'N/A',
                'payment_status' => $order->payments->first()?->status ?? 'pending',
                'paid_at' => $order->payments->first()?->paid_at?->format('M d, Y H:i'),
                
                // Items
                'items' => $order->saleLines->map(function ($line) {
                    $variant = $line->variant;
                    $product = $variant->product;
                    
                    // Get variant attributes
                    $attributes = $variant->attributeValues
                        ->map(fn($av) => $av->attribute->name . ': ' . $av->value)
                        ->implode(', ');
                    
                    return [
                        'product_name' => $product->name,
                        'variant_sku' => $variant->sku,
                        'variant_attributes' => $attributes ?: null,
                        'price' => $line->price,
                        'quantity' => $line->qty,
                        'subtotal' => $line->price * $line->qty,
                        'image_url' => $product->image_url ?? null,
                    ];
                }),
            ];
        });

        return response()->json([
            'success' => true,
            'orders' => $orders,
        ]);
    }

    /**
     * Get single order details
     */
    public function show(Request $request, $id)
    {
        $userId = $request->user_id ?? Auth::id();
        
        if (!$userId) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }

        $order = Transaction::with([
            'saleLines.variant.product',
            'saleLines.variant.attributeValues.attribute',
            'payments'
        ])
        ->where('user_id', $userId)
        ->where('id', $id)
        ->first();

        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        return response()->json([
            'success' => true,
            'order' => [
                'id' => $order->id,
                'invoice_no' => $order->invoice_no,
                'total_amount' => $order->total_sell_price,
                'total_items' => $order->total_items,
                'status' => $order->status,
                'shipping_status' => $order->shipping_status,
                'shipping_address' => $order->shipping_address,
                'discount_amount' => $order->discount_amount,
                'shipping_charge' => $order->shipping_charge ?? 0,
                'lat' => $order->lat,
                'long' => $order->long,
                'created_at' => $order->created_at->format('Y-m-d H:i:s'),
                'formatted_date' => $order->created_at->format('M d, Y'),
                
                'payment' => [
                    'method' => $order->payments->first()?->method ?? 'N/A',
                    'status' => $order->payments->first()?->status ?? 'pending',
                    'amount' => $order->payments->first()?->amount ?? 0,
                    'paid_at' => $order->payments->first()?->paid_at?->format('M d, Y H:i'),
                ],
                
                'items' => $order->saleLines->map(function ($line) {
                    $variant = $line->variant;
                    $product = $variant->product;
                    
                    $attributes = $variant->attributeValues
                        ->map(fn($av) => $av->attribute->name . ': ' . $av->value)
                        ->implode(', ');
                    
                    return [
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'variant_id' => $variant->id,
                        'variant_sku' => $variant->sku,
                        'variant_attributes' => $attributes ?: null,
                        'price' => $line->price,
                        'quantity' => $line->qty,
                        'subtotal' => $line->price * $line->qty,
                        'image_url' => $product->image_url ?? null,
                    ];
                }),

                'summary' => [
                    'subtotal' => $order->total_sell_price + $order->discount_amount,
                    'discount' => $order->discount_amount,
                    'shipping' => $order->shipping_charge ?? 0,
                    'total' => $order->total_sell_price,
                ],
            ],
        ]);
    }
}