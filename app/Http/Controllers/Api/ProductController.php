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
            $product = Product::with([
                'category',
                'descriptionLines' => function ($query) {
                    $query->orderBy('sort_order');
                },
                'variants.attributes.attributeValue.attribute',
                'variants.discount' => function ($query) {
                    $query->where('active', true);
                }
            ])->findOrFail($id);

            return response()->json([
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