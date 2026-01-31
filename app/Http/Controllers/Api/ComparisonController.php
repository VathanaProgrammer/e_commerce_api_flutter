<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProductComparison;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ComparisonController extends Controller
{
    public function index(Request $request)
    {
        $sessionId = $request->header('X-Session-ID') ?? $request->session_id;
        
        $query = ProductComparison::with(['product.variants', 'product.category', 'product.reviews']);
        
        if (Auth::check()) {
            $query->where('user_id', Auth::id());
        } else {
            $query->where('session_id', $sessionId);
        }
        
        $comparisons = $query->get();
        
        $products = $comparisons->map(function ($comparison) {
            $product = $comparison->product;
            $product->avg_rating = $product->reviews->avg('rating');
            $product->review_count = $product->reviews->count();
            return $product;
        });

        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id'
        ]);

        $sessionId = $request->header('X-Session-ID') ?? $request->session_id ?? Str::uuid();
        
        // Limit to 4 products for comparison
        $query = ProductComparison::query();
        
        if (Auth::check()) {
            $count = $query->where('user_id', Auth::id())->count();
            $existing = ProductComparison::where('user_id', Auth::id())
                ->where('product_id', $request->product_id)
                ->first();
        } else {
            $count = $query->where('session_id', $sessionId)->count();
            $existing = ProductComparison::where('session_id', $sessionId)
                ->where('product_id', $request->product_id)
                ->first();
        }

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'Product already in comparison list'
            ], 400);
        }

        if ($count >= 4) {
            return response()->json([
                'success' => false,
                'message' => 'Maximum 4 products can be compared at once'
            ], 400);
        }

        $data = ['product_id' => $request->product_id];
        
        if (Auth::check()) {
            $data['user_id'] = Auth::id();
        } else {
            $data['session_id'] = $sessionId;
        }

        $comparison = ProductComparison::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Product added to comparison',
            'data' => $comparison->load('product'),
            'session_id' => $sessionId
        ]);
    }

    public function remove($productId, Request $request)
    {
        $sessionId = $request->header('X-Session-ID') ?? $request->session_id;
        
        $query = ProductComparison::where('product_id', $productId);
        
        if (Auth::check()) {
            $query->where('user_id', Auth::id());
        } else {
            $query->where('session_id', $sessionId);
        }
        
        $comparison = $query->first();

        if (!$comparison) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found in comparison list'
            ], 404);
        }

        $comparison->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product removed from comparison'
        ]);
    }

    public function clear(Request $request)
    {
        $sessionId = $request->header('X-Session-ID') ?? $request->session_id;
        
        if (Auth::check()) {
            ProductComparison::where('user_id', Auth::id())->delete();
        } else {
            ProductComparison::where('session_id', $sessionId)->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Comparison list cleared'
        ]);
    }
}
