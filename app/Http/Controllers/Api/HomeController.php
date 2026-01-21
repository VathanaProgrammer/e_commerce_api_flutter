<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Business;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Favorite;
use App\Models\Category;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $category = $request->query('category');
        $search   = $request->query('search');
        $userId   = $request->query('user_id');

        // Categories 
        $categories = Category::select('id', 'name')->get();

        // business info
        $business = Business::select('id', 'name', 'logo')->first();

        // Make sure user_id is valid (numeric) and not empty
        $favorites = [];
        if (!empty($userId) && is_numeric($userId)) {
            $favorites = Favorite::where('user_id', $userId)
                ->pluck('product_id') // just the product IDs
                ->toArray();
        }

        // Products query
        $products = Product::with([
            'category:id,name',
            'discounts' => fn($q) => $q->where('active', true)->limit(1),
            'variants',
        ])
            ->when(
                $category && $category !== 'All',
                fn($q) =>
                $q->whereHas('category', fn($c) => $c->where('name', $category))
            )
            ->when($search, fn($q) => $q->where('name', 'LIKE', "%{$search}%"))
            ->get()
            ->map(function ($product) use ($favorites) {
                $discount = $product->discounts->first();
                $variant = $product->variants->first();
                $price = $variant ? $variant->price : 0;

                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => (float) number_format((float) $price, 2, '.', ''),
                    'image_url' => $product->image_url ?? '',
                    'category' => $product->category ? $product->category->name : null,
                    'discount' => $discount ? [
                        'id' => $discount->id,
                        'value' => $discount->value,
                        'is_percentage' => (bool) $discount->is_percentage,
                    ] : null,
                    'is_featured' => (bool) $product->is_featured,
                    'is_recommended' => (bool) $product->is_recommended,
                    'is_favorite' => in_array($product->id, $favorites), 
                ];
            });


        return response()->json([
            'categories' => $categories,
            'products' => $products,
            'business' => $business,
        ]);
    }
}