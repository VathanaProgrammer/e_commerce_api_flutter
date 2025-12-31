@extends('layouts.app')

@section('content')
    <x-widget title="Edit Product">
        <form method="POST" action="{{ route('products.update', $product->id) }}" id="form_update_product">
            @csrf
            @method('PUT')

            <input type="hidden" name="product_id" value="{{ $product->id }}">

            <div class="row g-3 align-items-end">

                {{-- Product Name --}}
                <div class="col-auto">
                    <label class="form-label small mb-1">Product Name</label>
                    <input type="text" name="name" class="form-control form-control-sm rounded-0"
                        value="{{ old('name', $product->name) }}" placeholder="Product name">
                </div>

                {{-- Category --}}
                <div class="col-auto">
                    <label class="form-label small mb-1">Category</label>
                    <select name="category_id" class="form-select form-select-sm rounded-0">
                        <option value="">Select Category</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}"
                                {{ $category->id == $product->category_id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

            </div>

            {{-- Description Lines --}}
            <div class="mt-3">
                <label class="form-label small mb-1">Product Description Lines</label>
                <div id="descriptionLines">
                    @foreach ($product->descriptionLines as $line)
                        <div class="row g-2 mb-2 align-items-center">
                            <div class="col-auto">
                                <input type="text" name="description_lines[]"
                                    class="form-control form-control-sm rounded-0" value="{{ $line->text }}"
                                    placeholder="Description line">
                            </div>
                            <div class="col-auto">
                                <button type="button" class="btn btn-sm btn-outline-danger removeLine">Remove</button>
                            </div>
                        </div>
                    @endforeach
                </div>
                <button type="button" class="btn btn-sm btn-outline-primary" id="addLine">Add Line</button>
            </div>

            {{-- Attributes --}}
            <div class="mt-3">
                <label class="form-label small mb-1">Attributes</label>
                <div id="attributesSection">
                    @foreach ($attributes as $attr)
                        <div class="attribute-box border p-2 mb-2" data-id="{{ $attr->id }}">
                            <label class="small">{{ $attr->name }}</label>
                            <div class="values">
                                @foreach ($attr->values as $val)
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input attribute-check" type="checkbox"
                                            name="product_attributes[{{ $attr->id }}][]" value="{{ $val->id }}"
                                            @foreach ($product->variants as $v)
                                     @if ($v->attributeValues->contains('id', $val->id)) checked @endif @endforeach>
                                        <label class="form-check-label">{{ $val->value }}</label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>


            {{-- Variants --}}
            <div class="mt-3">
                <label class="form-label small mb-1">Variants</label>
                <div id="variantsSection">
                    @foreach ($product->variants as $variant)
                        <div class="variant-block border p-2 mb-2">
                            <div class="row g-2 align-items-center">
                                <div class="col-auto">
                                    <input type="text" name="variant_sku[]"
                                        class="form-control form-control-sm rounded-0" value="{{ $variant->sku }}"
                                        placeholder="SKU">
                                </div>
                                <div class="col-auto">
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">$</span>
                                        <input type="number" name="variant_price[]" class="form-control rounded-0"
                                            value="{{ $variant->price }}" placeholder="Price">
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <button type="button" class="btn btn-sm btn-outline-danger removeVariant">Remove
                                        Variant</button>
                                </div>
                            </div>

                            {{-- Nested attribute values --}}
                            <div class="mt-2 ps-4">
                                @foreach ($variant->attributeValues as $attrValue)
                                    <div class="row g-2 mb-1 align-items-center">
                                        <div class="col-auto">
                                            <input type="text" class="form-control form-control-sm rounded-0"
                                                value="{{ $attrValue->attribute->name }}: {{ $attrValue->value }}"
                                                readonly>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach

                </div>
                <button type="button" class="btn btn-sm btn-outline-warning" id="addVariant">Add Variant</button>
            </div>

            {{-- DISCOUNTS --}}
            <div class="mt-4">
                <label class="form-label small mb-1">Discount</label>
                <div class="row g-2 align-items-center">

                    @if ($product->discounts->count())
                        {{-- Show existing discounts as selectable boxes --}}
                        @foreach ($product->discounts as $discount)
                            <div class="col-12 col-md-6 mb-2">
                                <div class="discount-box border p-2 rounded {{ $discount->active ? 'selected' : '' }}"
                                    data-id="{{ $discount->id }}" style="cursor:pointer;">
                                    <strong>{{ $discount->name }}</strong> — {{ $discount->value }}
                                    {{ $discount->is_percentage ? '%' : '$' }}
                                    @if (!$discount->active)
                                        <span class="text-muted">(Inactive)</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                        <input type="hidden" name="discount_id" id="discount_id"
                            value="{{ $product->discounts->where('active', true)->first()?->id }}">
                    @else
                        {{-- No discount yet: show inputs to add one --}}
                        <div class="row g-2 align-items-center" id="addDiscountSection">
                            <div class="col-auto">
                                <input type="text" name="discount[name]" class="form-control form-control-sm rounded-0"
                                    placeholder="Discount Name">
                            </div>
                            <div class="col-auto">
                                <input type="number" step="0.01" name="discount[value]"
                                    class="form-control form-control-sm rounded-0" placeholder="Value">
                            </div>
                            <div class="col-auto">
                                <select name="discount[is_percentage]" class="form-select form-select-sm rounded-0">
                                    <option value="1">Percentage %</option>
                                    <option value="0">Fixed Amount $</option>
                                </select>
                            </div>
                            <div class="col-auto d-flex align-items-center">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="discount[active]"
                                        value="1" checked>
                                    <label class="form-check-label small">Active</label>
                                </div>
                            </div>
                        </div>
                    @endif

                </div>
            </div>

            {{-- Submit --}}
            <div class="mt-4">
                <button type="submit" class="btn btn-sm btn-success rounded-0">Update Product</button>
            </div>

        </form>

    </x-widget>
@endsection
@section('scripts')
    <script>
        $(document).ready(function() {
            $('.discount-box').on('click', function() {
                // Remove selection from all boxes
                $('.discount-box').removeClass('selected');
                // Add selection to clicked box
                $(this).addClass('selected');
                // Update hidden input value
                $('#discount_id').val($(this).data('id'));
            });
            $('#form_update_product').on('submit', function(e) {
                e.preventDefault(); // stop default submit

                let form = $(this);
                let url = form.attr('action'); // route('products.update', $product->id)
                let formData = form.serialize();

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: formData,
                    success: function(res) {
                        if (res.success) {
                            toastr.success('Product updated successfully!');
                            // Redirect to products index after a short delay
                            setTimeout(function() {
                                window.location.href = res.location;
                            }, 500);
                        } else {
                            toastr.error(res.msg ||
                                'Failed to update product. Please try again.');
                        }
                    },
                    error: function(err) {
                        // show errors
                        if (err.responseJSON && err.responseJSON.errors) {
                            let messages = Object.values(err.responseJSON.errors).flat().join(
                                '<br>');
                            toastr.error(messages);
                        } else {
                            toastr.error('Something went wrong.');
                        }
                    }
                });
            });


            // Add Description Line
            $('#addLine').off('click').on('click', function() {
                $('#descriptionLines').append(`
            <div class="row g-2 mb-2 align-items-center">
                <div class="col-auto">
                    <input type="text" name="description_lines[]" class="form-control form-control-sm rounded-0" placeholder="Description line">
                </div>
                <div class="col-auto">
                    <button type="button" class="btn btn-sm btn-outline-danger removeLine">Remove</button>
                </div>
            </div>
        `);
            });

            // Remove Description Line
            $(document).on('click', '.removeLine', function() {
                $(this).closest('.row').remove();
            });

            // Add Attribute
            $('#addAttr').off('click').on('click', function() {
                $('#attributesSection').append(`
            <div class="row g-2 mb-2 align-items-center">
                <div class="col-auto">
                    <input type="text" name="attributes[]" class="form-control form-control-sm rounded-0" placeholder="Attribute name">
                </div>
                <div class="col-auto">
                    <button type="button" class="btn btn-sm btn-outline-danger removeAttr">Remove</button>
                </div>
            </div>
        `);
            });

            // Remove Attribute
            $(document).on('click', '.removeAttr', function() {
                $(this).closest('.row').remove();
            });

            // Add Variant
            $('#addVariant').off('click').on('click', function() {
                $('#variantsSection').append(`
            <div class="row g-2 mb-2 align-items-center">
                <div class="col-auto">
                    <input type="text" name="variant_sku[]" class="form-control form-control-sm rounded-0" placeholder="SKU">
                </div>
                <div class="col-auto">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text">$</span>
                        <input type="number" name="variant_price[]" class="form-control rounded-0" placeholder="Price">
                    </div>
                </div>
                <div class="col-auto">
                    <button type="button" class="btn btn-sm btn-outline-danger removeVariant">Remove</button>
                </div>
            </div>
        `);
            });

            // Remove Variant
            $(document).on('click', '.removeVariant', function() {
                $(this).closest('.row').remove();
            });

        });
    </script>
@endsection

@section('styles')
    <style>

    </style>
@endsection
