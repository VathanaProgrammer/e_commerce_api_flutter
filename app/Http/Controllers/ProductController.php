<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductDescriptionLine;
use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\ProductVariant;
use App\Models\ProductVariantAttribute;
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


    public function create()
    {
        $categories = Category::all();
        return view('products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        Log::info('Storing new product', ['request' => $request->all()]);

        // Validate request
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description_lines.*' => 'nullable|string|max:500',
            'attributes.*' => 'nullable|string|max:100',
            'variant_sku.*' => 'nullable|string|max:100',
            'variant_price.*' => 'nullable|numeric|min:0',
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

            // 3. Save attributes
            $attributeIds = [];
            if ($request->attributes) {
                foreach ($request->attributes as $attrName) {
                    if ($attrName) {
                        $attribute = Attribute::firstOrCreate(['name' => $attrName]);
                        $attributeIds[] = $attribute->id;
                    }
                }
            }

            // 4. Save product variants
            if ($request->variant_sku && $request->variant_price) {
                foreach ($request->variant_sku as $index => $sku) {
                    $price = $request->variant_price[$index];
                    if ($sku || $price) {
                        $variant = ProductVariant::create([
                            'product_id' => $product->id,
                            'sku' => $sku,
                            'price' => $price,
                        ]);

                        // Assign attribute values to this variant
                        foreach ($attributeIds as $attrId) {
                            $attrValue = AttributeValue::firstOrCreate([
                                'attribute_id' => $attrId,
                                'value' => 'Default', // placeholder
                            ]);

                            ProductVariantAttribute::create([
                                'product_variant_id' => $variant->id,
                                'attribute_value_id' => $attrValue->id,
                            ]);
                        }
                    }
                }
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
}