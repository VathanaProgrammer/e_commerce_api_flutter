@extends('layouts.app')

@section('content')
    <x-widget title="Edit Product">

        <form method="POST" action="{{ route('products.update', $product->id) }}">
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
                    @foreach ($product->attributes as $attr)
                        <div class="row g-2 mb-2 align-items-center">
                            <div class="col-auto">
                                <input type="text" name="attributes[]" class="form-control form-control-sm rounded-0"
                                    value="{{ $attr->name }}" placeholder="Attribute name">
                            </div>
                            <div class="col-auto">
                                <button type="button" class="btn btn-sm btn-outline-danger removeAttr">Remove</button>
                            </div>
                        </div>
                    @endforeach
                </div>
                <button type="button" class="btn btn-sm btn-outline-secondary" id="addAttr">Add Attribute</button>
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
