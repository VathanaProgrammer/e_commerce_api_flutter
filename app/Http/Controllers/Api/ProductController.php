<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductDiscount;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Simple test endpoint to check if product exists
     */
    public function test($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found',
                'product_id' => $id,
                'all_product_ids' => Product::pluck('id')->toArray()
            ], 404);
        }

        return response()->json([
            'success' => true,
            'product' => $product,
            'has_category' => $product->category_id !== null,
            'category' => $product->category ?? null,
            'variants_count' => $product->variants()->count(),
            'description_lines_count' => $product->descriptionLines()->count(),
        ]);
    }

    /**
     * Get product details with variants and attributes
     */
    public function show($id)
    {
        try {
            // First check if product exists
            $product = Product::find($id);

            if (!$product) {
                return response()->json([
                    "success" => false,
                    'message' => 'Product not found',
                    'product_id' => $id
                ], 404);
            }

            // Load relationships - use 'value' instead of 'attributeValue'
            $product->load([
                'category',
                'descriptionLines' => function ($query) {
                    $query->orderBy('sort_order');
                },
                'variants.attributes.value.attribute',  // Changed here
            ]);

            return response()->json([
                "success" => true,
                'data' => [
                    'id' => $product->id,
                    'name' => $product->name,
                    'image_url' => $product->imageUrl,
                    'category_id' => $product->category_id,
                    'category_name' => $product->category ? $product->category->name : null,
                    'description_lines' => $product->descriptionLines->map(function ($line) {
                        return [
                            'text' => $line->text,
                            'sort_order' => $line->sort_order,
                        ];
                    }),
                    'variants' => $product->variants->map(function ($variant) {
                        // Get active discount for this specific variant
                        $discount = ProductDiscount::where('product_variant_id', $variant->id)
                            ->where('active', true)
                            ->first();

                        // If no variant-specific discount, check product-level discount
                        if (!$discount) {
                            $discount = ProductDiscount::where('product_id', $variant->product_id)
                                ->whereNull('product_variant_id')
                                ->where('active', true)
                                ->first();
                        }

                        return [
                            'id' => $variant->id,
                            'product_id' => $variant->product_id,
                            'sku' => $variant->sku,
                            'price' => (float) $variant->price,
                            'attributes' => $variant->attributes->map(function ($pivotAttr) {
                                return [
                                    'attribute_value_id' => $pivotAttr->value->id,  // Changed here
                                    'attribute_name' => $pivotAttr->value->attribute->name,  // Changed here
                                    'value' => $pivotAttr->value->value,  // Changed here
                                ];
                            }),
                            'discount' => $discount ? [
                                'id' => $discount->id,
                                'name' => $discount->name,
                                'value' => (float) $discount->value,
                                'is_percentage' => (bool) $discount->is_percentage,
                                'active' => (bool) $discount->active,
                            ] : null,
                        ];
                    }),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "success" => false,
                'message' => 'Error loading product',
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ], 500);
        }
    }
}