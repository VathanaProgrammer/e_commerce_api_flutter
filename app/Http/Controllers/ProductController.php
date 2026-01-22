<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductDescriptionLine;
use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\ProductVariant;
use App\Models\ProductVariantAttribute;
use App\Models\ProductDiscount;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Category;
use Yajra\DataTables\Facades\DataTables;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        // Only return Blade view for normal requests
        return view('products.index');
    }

    public function data(Request $request)
    {
        if ($request->ajax()) {
            $products = Product::with(['category', 'variants.attributeValues.attribute', 'descriptionLines'])
                ->select('products.*');

            return DataTables::of($products)
                ->addColumn('category', fn($product) => $product->category?->name ?? '<span class="badge bg-secondary">No Category</span>')
                ->addColumn('status', function ($product) {
                    $badges = [];

                    if ($product->active) {
                        $badges[] = '<span class="status-badge text-white bg-success">Active</span>';
                    } else {
                        $badges[] = '<span class="status-badge text-white bg-secondary">Inactive</span>';
                    }

                    if ($product->is_featured) {
                        $badges[] = '<span class="status-badge bg-warning text-dark">Featured</span>';
                    }

                    if ($product->is_recommended) {
                        $badges[] = '<span class="status-badge bg-info text-dark">Recommended</span>';
                    }

                    return implode(' ', $badges);
                })
                ->addColumn('variants', function ($product) {
                    if ($product->variants->isEmpty()) {
                        return '<span class="text-muted">No Variants</span>';
                    }

                    $count = $product->variants->count();
                    $html = '<div class="mb-1"><span class="variant-count-badge">' . $count . ' Variant' . ($count > 1 ? 's' : '') . '</span></div>';
                    $html .= '<div class="variants-scrollable">';

                    foreach ($product->variants as $variant) {
                        $attributes = $variant->attributeValues->map(function ($attrValue) {
                            return '<span class="variant-badge">' . $attrValue->attribute->name . ': ' . $attrValue->value . '</span>';
                        })->join(' ');

                        $html .= '<div class="variant-item">';
                        $html .= '<div><span class="variant-sku">SKU: ' . ($variant->sku ?: 'N/A') . '</span> | ';
                        $html .= '<span class="variant-price">$' . number_format($variant->price, 2) . '</span></div>';

                        if ($attributes) {
                            $html .= '<div class="variant-attrs">' . $attributes . '</div>';
                        }

                        $html .= '</div>';
                    }

                    $html .= '</div>';
                    return $html;
                })
                ->addColumn('description', function ($product) {
                    if ($product->descriptionLines->isEmpty()) {
                        return '<span class="text-muted">No Description</span>';
                    }

                    $html = '';
                    foreach ($product->descriptionLines->take(3) as $line) {
                        $html .= '<div class="desc-line">' . e($line->text) . '</div>';
                    }

                    if ($product->descriptionLines->count() > 3) {
                        $remaining = $product->descriptionLines->count() - 3;
                        $html .= '<small class="text-muted">+' . $remaining . ' more...</small>';
                    }

                    return $html;
                })
                ->addColumn('action', function ($product) {
                    return '
                    <div class="btn-group btn-group-sm" role="group">
                        <a href="' . route('products.edit', $product->id) . '" 
                           class="btn btn-outline-primary" 
                           title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        <a href="' . route('products.destroy', $product->id) . '" 
                           class="btn btn-outline-danger delete-product" 
                           title="Delete">
                            <i class="fas fa-trash"></i>
                        </a>
                    </div>
                ';
                })
                ->addColumn('image_url', fn($product) => $product->image_url)
                ->rawColumns(['category', 'status', 'variants', 'description', 'action'])
                ->make(true);
        }

        return abort(404);
    }

    public function edit($id)
    {
        $product = Product::with([
            'category',
            'descriptionLines',
            'variants.attributeValues.attribute',
            'activeDiscount', // only the active one
        ])->find($id);

        if (!$product) {
            return redirect()->back()->with('error', 'Product not found');
        }

        $attributes = Attribute::all();
        $categories = Category::all();
        Log::info('Editing product', ['product' => $product]);
        return view('products.edit', compact('product', 'categories', 'attributes'));
    }

    public function create()
    {
        $categories = Category::all();
        $attributes = Attribute::all();
        return view('products.create', compact('categories', 'attributes'));
    }

    public function store(Request $request)
    {
        Log::info('Storing new product', ['request' => $request->all()]);

        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description_lines.*' => 'nullable|string|max:500',
            'variants.*.sku' => 'nullable|string|max:100',
            'variants.*.price' => ['nullable', 'numeric', 'min:0', 'regex:/^\d+(\.\d{1,2})?$/'],
            'variants.*.attributes.*' => 'nullable|exists:attribute_values,id',
            'discount.value' => 'nullable|numeric|min:0',
            'image' => 'nullable|image|max:2048',
            'is_recommended' =>  'nullable|boolean',
            'is_featured' => 'nullable|boolean',
            'active' => 'nullable|boolean'

        ]);

        DB::beginTransaction();

        try {
            // 1. Handle image upload
            $imageName = null;
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '_' . $image->getClientOriginalName();
                $image->move(public_path('uploads/products'), $imageName);
            }
            // 1. Create product
            $product = Product::create([
                'name' => $request->name,
                'category_id' => $request->category_id,
                'image_url' => $imageName ? '/uploads/products/' . $imageName : null,
                'is_recommended' => $request->has('is_recommended'),
                'is_featured' => $request->has('is_featured'),
                'active' => $request->has('active')
            ]);

            // 2. Save description lines
            if ($request->description_lines) {
                foreach ($request->description_lines as $index => $line) {
                    if ($line) {
                        ProductDescriptionLine::create([
                            'product_id' => $product->id,
                            'text' => $line,
                            'sort_order' => $index,
                        ]);
                    }
                }
            }

            // 3. Save variants and link attribute values
            if ($request->variants) {
                foreach ($request->variants as $variantData) {
                    $variant = ProductVariant::create([
                        'product_id' => $product->id,
                        'sku' => $variantData['sku'] ?? null,
                        'price' => $variantData['price'] ?? 0,
                    ]);

                    if (isset($variantData['attributes'])) {
                        foreach ($variantData['attributes'] as $valueId) {
                            ProductVariantAttribute::create([
                                'product_variant_id' => $variant->id,
                                'attribute_value_id' => $valueId,
                            ]);
                        }
                    }
                }
            }
            if ($request->filled('discount.value')) {
                ProductDiscount::create([
                    'name' => $request->discount['name']
                        ?? $product->name . ' Discount',
                    'product_id' => $product->id,
                    'value' => $request->discount['value'],
                    'is_percentage' => (bool) ($request->discount['is_percentage'] ?? true),
                    'active' => (bool) ($request->discount['active'] ?? true),
                ]);
            }


            DB::commit();
            return response()->json([
                'success' => true,
                'msg' => 'Product created successfully.',
                'location' => route('products.index')
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to store product', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all(),
            ]);
            return response()->json([
                'success' => false,
                'msg' => 'Failed to create product. Please try again.'
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        Log::info('Updating product', ['id' => $id, 'request' => $request->all()]);
        $product = Product::with(['descriptionLines', 'variants.attributeValues'])->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description_lines.*' => 'nullable|string|max:500',
            'variants.*.sku' => 'nullable|string|max:100',
            'variants.*.price' => ['nullable', 'numeric', 'min:0', 'regex:/^\d+(\.\d{1,2})?$/'],
            'variants.*.attributes.*' => 'nullable|exists:attribute_values,id',
            'is_recommended' =>  'nullable|boolean',
            'is_featured' => 'nullable|boolean',
            'active' => 'nullable|boolean'
        ]);

        DB::beginTransaction();
        try {
            // 1. Handle image update
            if ($request->hasFile('image')) {
                // Delete old image if exists
                if ($product->image_url && file_exists(public_path('uploads/products/' . $product->image_url))) {
                    unlink(public_path('uploads/products/' . $product->image_url));
                }
                $image = $request->file('image');
                $imageName = time() . '_' . $image->getClientOriginalName();
                $image->move(public_path('uploads/products'), $imageName);
                $product->image_url = $imageName;
            }
            $product->update([
                'name' => $request->name,
                'category_id' => $request->category_id,
                'is_recommended' => $request->has('is_recommended'),
                'is_featured' => $request->has('is_featured'),
                'active' => $request->has('active')
            ]);

            // 1. Update description lines
            $product->descriptionLines()->delete();
            if ($request->description_lines) {
                foreach ($request->description_lines as $index => $line) {
                    if ($line) {
                        ProductDescriptionLine::create([
                            'product_id' => $product->id,
                            'text' => $line,
                            'sort_order' => $index,
                        ]);
                    }
                }
            }

            // 2. Update variants
            $product->variants()->delete(); // remove old variants + their attribute links
            if ($request->variants) {
                foreach ($request->variants as $variantData) {
                    $variant = ProductVariant::create([
                        'product_id' => $product->id,
                        'sku' => $variantData['sku'] ?? null,
                        'price' => $variantData['price'] ?? 0,
                    ]);

                    if (isset($variantData['attributes'])) {
                        foreach ($variantData['attributes'] as $valueId) {
                            ProductVariantAttribute::create([
                                'product_variant_id' => $variant->id,
                                'attribute_value_id' => $valueId,
                            ]);
                        }
                    }
                }
            }

            $discountId = $request->input('discount_id');
            $discountData = $request->input('discount');

            if ($discountId) {
                // Case: user selected an existing discount
                $product->discounts()->update(['active' => false]);
                $product->discounts()->where('id', $discountId)->update(['active' => true]);
            } elseif ($discountData && !empty($discountData['value'])) {
                // Case: user added a new discount (no existing selected)
                // Deactivate all old discounts
                $product->discounts()->update(['active' => false]);

                // Create new discount
                ProductDiscount::create([
                    'product_id' => $product->id,
                    'name' => $discountData['name'] ?? $product->name . ' Discount',
                    'value' => $discountData['value'],
                    'is_percentage' => $discountData['is_percentage'] ?? true,
                    'active' => $discountData['active'] ?? true,
                ]);
            } else {
                // No discount selected or added → deactivate all
                $product->discounts()->update(['active' => false]);
            }

            DB::commit();
            return response()->json(['success' => true, 'msg' => 'Product updated successfully.', 'location' => route('products.index')], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update product', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all(),
            ]);
            return response()->json(['success' => false, 'msg' => 'Failed to update product. Please try again.'], 500);
        }
    }
}