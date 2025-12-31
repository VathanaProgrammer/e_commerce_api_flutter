<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $category = $request->query('category');
        $search   = $request->query('search');

        // Categories (for tabs)
        $categories = Category::select('id', 'name')->get();

        // Products query
        $products = Product::with([
                'category:id,name',
                'discounts' => function ($q) {
                    $q->where('active', true)->limit(1);
                }
            ])
            ->when($category && $category !== 'All', function ($q) use ($category) {
                $q->whereHas('category', function ($c) use ($category) {
                    $c->where('name', $category);
                });
            })
            ->when($search, function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%");
            })
            ->get()
            ->map(function ($product) {
                $discount = $product->discounts->first();

                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => (int) $product->price,
                    'image' => $product->image ?? '',
                    'discount' => $discount ? [
                        'id' => $discount->id,
                        'value' => $discount->value,
                        'is_percentage' => (bool) $discount->is_percentage,
                    ] : null,
                ];
            });

        return response()->json([
            'categories' => $categories,
            'products' => $products,
        ]);
    }
}