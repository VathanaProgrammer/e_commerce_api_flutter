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
            $products = Product::with(['category', 'variants', 'descriptionLines'])->select('products.*');

            return DataTables::of($products)
                ->addColumn('category', fn($product) => $product->category?->name ?? 'No Category')
                ->addColumn('variants', function ($product) {
                    $html = '';
                    foreach ($product->variants as $variant) {
                        $html .= "SKU: {$variant->sku}, Price: {$variant->price}<br>";
                    }
                    return $html ?: 'No Variants';
                })
                ->addColumn('description', fn($product) => $product->descriptionLines->pluck('text')->implode('<br>') ?: 'No Description')
                ->addColumn('action', function ($product) {
                    return '<a href="' . route('products.edit', $product->id) . '" class="btn btn-sm btn-primary me-1">Edit</a>
                        <a href="' . route('products.destroy', $product->id) . '" class="btn btn-sm btn-danger delete-product">Delete</a>';
                })
                ->rawColumns(['variants', 'description', 'action'])
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
            'variants.*.price' => 'nullable|numeric|min:0',
            'variants.*.attributes.*' => 'nullable|exists:attribute_values,id',
        ]);

        DB::beginTransaction();

        try {
            // 1. Create product
            $product = Product::create([
                'name' => $request->name,
                'category_id' => $request->category_id,
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
            if ($request->has('discount.value')) {
                ProductDiscount::create([
                    'name' => $request->discount['name'] ?? $product->name . ' Discount',
                    'product_id' => $product->id,
                    'value' => $request->discount['value'],
                    'is_percentage' => $request->discount['is_percentage'] ?? true,
                    'active' => $request->discount['active'] ?? true,
                ]);
            }

            DB::commit();
            return redirect()->back()->with('success', 'Product created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to store product', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all(),
            ]);
            return redirect()->back()->with('error', 'Failed to create product. Please try again.');
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
            'variants.*.price' => 'nullable|numeric|min:0',
            'variants.*.attributes.*' => 'nullable|exists:attribute_values,id',
        ]);

        DB::beginTransaction();
        try {
            $product->update([
                'name' => $request->name,
                'category_id' => $request->category_id,
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