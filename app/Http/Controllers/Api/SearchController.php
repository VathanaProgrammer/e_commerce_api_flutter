<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $query = Product::query()->active();

        // Text search
        if ($request->has('q') && $request->q) {
            $searchTerm = $request->q;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhereHas('descriptionLines', function ($q) use ($searchTerm) {
                      $q->where('line', 'like', "%{$searchTerm}%");
                  });
            });
        }

        // Category filter
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Price range filter
        if ($request->has('min_price')) {
            $query->whereHas('variants', function ($q) use ($request) {
                $q->where('price', '>=', $request->min_price);
            });
        }

        if ($request->has('max_price')) {
            $query->whereHas('variants', function ($q) use ($request) {
                $q->where('price', '<=', $request->max_price);
            });
        }

        // Rating filter
        if ($request->has('min_rating')) {
            $query->whereHas('reviews', function ($q) use ($request) {
                $q->selectRaw('AVG(rating) as avg_rating')
                  ->groupBy('product_id')
                  ->havingRaw('AVG(rating) >= ?', [$request->min_rating]);
            });
        }

        // Featured/Recommended filters
        if ($request->has('is_featured') && $request->is_featured) {
            $query->where('is_featured', true);
        }

        if ($request->has('is_recommended') && $request->is_recommended) {
            $query->where('is_recommended', true);
        }

        // In stock filter
        if ($request->has('in_stock') && $request->in_stock) {
            $query->whereHas('variants', function ($q) {
                $q->where('stock', '>', 0);
            });
        }

        // Sorting
        $sortBy = $request->sort_by ?? 'created_at';
        $sortOrder = $request->sort_order ?? 'desc';

        switch ($sortBy) {
            case 'price_low':
                $query->join('product_variants', 'products.id', '=', 'product_variants.product_id')
                      ->select('products.*')
                      ->orderBy('product_variants.price', 'asc');
                break;
            case 'price_high':
                $query->join('product_variants', 'products.id', '=', 'product_variants.product_id')
                      ->select('products.*')
                      ->orderBy('product_variants.price', 'desc');
                break;
            case 'rating':
                $query->withAvg('reviews', 'rating')
                      ->orderBy('reviews_avg_rating', 'desc');
                break;
            case 'popular':
                $query->withCount('favoritedBy')
                      ->orderBy('favorited_by_count', 'desc');
                break;
            default:
                $query->orderBy($sortBy, $sortOrder);
        }

        $products = $query->with([
            'category',
            'variants',
            'activeDiscount',
            'reviews' => function ($q) {
                $q->approved()->limit(5);
            }
        ])
        ->withAvg('reviews', 'rating')
        ->withCount('reviews')
        ->paginate($request->per_page ?? 20);

        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }

    public function suggestions(Request $request)
    {
        $request->validate([
            'q' => 'required|string|min:2'
        ]);

        $suggestions = Product::active()
            ->where('name', 'like', "%{$request->q}%")
            ->limit(10)
            ->get(['id', 'name', 'image_url']);

        return response()->json([
            'success' => true,
            'data' => $suggestions
        ]);
    }

    public function filters()
    {
        $categories = \App\Models\Category::withCount('products')->get();
        
        $priceRange = \App\Models\ProductVariant::selectRaw('MIN(price) as min_price, MAX(price) as max_price')
            ->first();

        return response()->json([
            'success' => true,
            'data' => [
                'categories' => $categories,
                'price_range' => $priceRange,
                'sort_options' => [
                    ['value' => 'created_at', 'label' => 'Newest'],
                    ['value' => 'price_low', 'label' => 'Price: Low to High'],
                    ['value' => 'price_high', 'label' => 'Price: High to Low'],
                    ['value' => 'rating', 'label' => 'Highest Rated'],
                    ['value' => 'popular', 'label' => 'Most Popular']
                ]
            ]
        ]);
    }
}
