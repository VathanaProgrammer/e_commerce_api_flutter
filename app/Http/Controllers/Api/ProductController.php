<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
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

            // Load relationships one by one to see which fails
            $product->load('category');
            $product->load('descriptionLines');
            $product->load('variants');
            
            // Try loading variant relationships if variants exist
            if ($product->variants->isNotEmpty()) {
                foreach ($product->variants as $variant) {
                    try {
                        $variant->load('attributes.attributeValue.attribute');
                    } catch (\Exception $e) {
                        // Variants exist but no attributes - that's ok
                    }
                }
            }

            return response()->json([
                "success" => true,
                'data' => [
                    'id' => $product->id,
                    'name' => $product->name,
                    'image_url' => $product->image_url,
                    'category_id' => $product->category_id,
                    'category_name' => $product->category->name,
                    'description_lines' => $product->descriptionLines->map(function ($line) {
                        return [
                            'text' => $line->text,
                            'sort_order' => $line->sort_order,
                        ];
                    }),
                    'variants' => $product->variants->map(function ($variant) {
                        $discount = $variant->discount()->where('active', true)->first();
                        
                        return [
                            'id' => $variant->id,
                            'product_id' => $variant->product_id,
                            'sku' => $variant->sku,
                            'price' => $variant->price,
                            'attributes' => $variant->attributes->map(function ($pivotAttr) {
                                return [
                                    'attribute_value_id' => $pivotAttr->attributeValue->id,
                                    'attribute_name' => $pivotAttr->attributeValue->attribute->name,
                                    'value' => $pivotAttr->attributeValue->value,
                                ];
                            }),
                            'discount' => $discount ? [
                                'id' => $discount->id,
                                'name' => $discount->name,
                                'value' => $discount->value,
                                'is_percentage' => $discount->is_percentage,
                                'active' => $discount->active,
                            ] : null,
                        ];
                    }),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Product not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }
}