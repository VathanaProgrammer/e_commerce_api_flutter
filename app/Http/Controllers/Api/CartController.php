<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $sessionId = $request->header('X-Session-ID') ?? $request->session_id;
        
        $query = Cart::with(['productVariant.product', 'productVariant.attributeValues.attribute']);
        
        if (Auth::check()) {
            $query->where('user_id', Auth::id());
        } else {
            $query->where('session_id', $sessionId);
        }
        
        $cartItems = $query->get();
        
        $subtotal = $cartItems->sum(function ($item) {
            return $item->quantity * $item->price_at_addition;
        });
        
        return response()->json([
            'success' => true,
            'data' => [
                'items' => $cartItems,
                'subtotal' => $subtotal,
                'item_count' => $cartItems->sum('quantity')
            ]
        ]);
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_variant_id' => 'required|exists:product_variants,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $variant = ProductVariant::findOrFail($request->product_variant_id);
        
        if ($variant->stock < $request->quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient stock available'
            ], 400);
        }

        $sessionId = $request->header('X-Session-ID') ?? $request->session_id ?? Str::uuid();
        
        $cartData = [
            'product_variant_id' => $request->product_variant_id,
            'quantity' => $request->quantity,
            'price_at_addition' => $variant->price
        ];

        if (Auth::check()) {
            $cartData['user_id'] = Auth::id();
            $existing = Cart::where('user_id', Auth::id())
                ->where('product_variant_id', $request->product_variant_id)
                ->first();
        } else {
            $cartData['session_id'] = $sessionId;
            $existing = Cart::where('session_id', $sessionId)
                ->where('product_variant_id', $request->product_variant_id)
                ->first();
        }

        if ($existing) {
            $newQuantity = $existing->quantity + $request->quantity;
            
            if ($variant->stock < $newQuantity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot add more items. Stock limit reached.'
                ], 400);
            }
            
            $existing->update(['quantity' => $newQuantity]);
            $cart = $existing;
        } else {
            $cart = Cart::create($cartData);
        }

        return response()->json([
            'success' => true,
            'message' => 'Item added to cart',
            'data' => $cart->load(['productVariant.product']),
            'session_id' => $sessionId
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        $cart = Cart::findOrFail($id);
        
        if ($cart->productVariant->stock < $request->quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient stock available'
            ], 400);
        }

        $cart->update(['quantity' => $request->quantity]);

        return response()->json([
            'success' => true,
            'message' => 'Cart updated',
            'data' => $cart->load(['productVariant.product'])
        ]);
    }

    public function remove($id)
    {
        $cart = Cart::findOrFail($id);
        $cart->delete();

        return response()->json([
            'success' => true,
            'message' => 'Item removed from cart'
        ]);
    }

    public function clear(Request $request)
    {
        $sessionId = $request->header('X-Session-ID') ?? $request->session_id;
        
        if (Auth::check()) {
            Cart::where('user_id', Auth::id())->delete();
        } else {
            Cart::where('session_id', $sessionId)->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Cart cleared'
        ]);
    }
}
