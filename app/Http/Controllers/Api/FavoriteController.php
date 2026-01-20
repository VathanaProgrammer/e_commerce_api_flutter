<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Favorite;
use App\Models\Product;

class FavoriteController extends Controller
{
    // Fetch all favorite product IDs for a given user
    public function index(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $favorites = Favorite::where('user_id', $request->user_id)
            ->pluck('product_id');

        return response()->json([
            'favorite_product_ids' => $favorites
        ]);
    }

    // Add a product to favorites
    public function add(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'product_id' => 'required|exists:products,id',
        ]);

        Favorite::firstOrCreate([
            'user_id' => $request->user_id,
            'product_id' => $request->product_id,
        ]);

        return response()->json(['message' => 'Added to favorites']);
    }

    // Remove a product from favorites
    public function remove(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'product_id' => 'required|exists:products,id',
        ]);

        Favorite::where('user_id', $request->user_id)
            ->where('product_id', $request->product_id)
            ->delete();

        return response()->json(['message' => 'Removed from favorites']);
    }
}