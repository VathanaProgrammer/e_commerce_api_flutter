@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <x-widget title="Edit Product">
            <form id="form_update_product" enctype="multipart/form-data" method="POST"
                action="{{ route('products.update', $product->id) }}">
                @csrf
                @method('PUT')

                <input type="hidden" name="product_id" value="{{ $product->id }}">

                <div class="row g-4">

                    {{-- Left Column: Basic Info + Image + Discount --}}
                    <div class="col-md-6">
                        <h6 class="mb-2">Basic Info</h6>

                        <div class="d-flex justify-content-between">
                            <div class="mb-3">
                                <label class="form-label small">Product Name</label>
                                <input type="text" name="name" class="form-control form-control-sm rounded-0"
                                    value="{{ old('name', $product->name) }}">
                            </div>
                            <div class="mb-3">
                                <div class="mb- form-check">
                                    <input type="checkbox" name="is_recommended" class="form-check-input" id="isRecommended"
                                        value="1"
                                        {{ old('is_recommended', $product->is_recommended ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="isRecommended">Recommended</label>
                                </div>

                                <div class="mb- form-check">
                                    <input type="checkbox" name="is_featured" class="form-check-input" id="isFeatured"
                                        value="1"
                                        {{ old('is_featured', $product->is_featured ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="isFeatured">Featured</label>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small">Category</label>
                            <select name="category_id" class="form-select form-select-sm rounded-0">
                                <option value="">Select</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}"
                                        {{ $category->id == $product->category_id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small">Product Image</label>
                            <input type="file" name="image" class="form-control form-control-sm rounded-0"
                                accept="image/*">
                            @if ($product->image)
                                <img id="imagePreview" src="{{ asset('uploads/products/' . $product->image) }}"
                                    class="img-fluid mt-2" style="max-height:150px;">
                            @else
                                <img id="imagePreview" src="" class="img-fluid mt-2"
                                    style="max-height:150px; display:none;">
                            @endif
                        </div>

                        <h6 class="mt-4 mb-2">Discount</h6>
                        <div class="row g-2 align-items-center">
                            <div class="col-auto">
                                <input type="text" name="discount[name]" class="form-control form-control-sm rounded-0"
                                    value="{{ old('discount.name', $product->discounts->first()?->name) }}"
                                    placeholder="Discount Name">
                            </div>
                            <div class="col-auto">
                                <input type="number" step="0.01" name="discount[value]"
                                    class="form-control form-control-sm rounded-0"
                                    value="{{ old('discount.value', $product->discounts->first()?->value) }}"
                                    placeholder="Value">
                            </div>
                            <div class="col-auto">
                                <select name="discount[is_percentage]" class="form-select form-select-sm rounded-0">
                                    <option value="1"
                                        {{ $product->discounts->first()?->is_percentage ? 'selected' : '' }}>
                                        Percentage %</option>
                                    <option value="0"
                                        {{ $product->discounts->first()?->is_percentage ? '' : 'selected' }}>Fixed Amount $
                                    </option>
                                </select>
                            </div>
                            <div class="col-auto d-flex align-items-center">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="discount[active]" value="1"
                                        {{ $product->discounts->first()?->active ? 'checked' : '' }}>
                                    <label class="form-check-label small">Active</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Right Column: Description + Attributes + Variants --}}
                    <div class="col-md-6">
                        <h6 class="mb-2">Descriptions</h6>
                        <div id="descriptionLines" class="mb-2">
                            @foreach ($product->descriptionLines as $line)
                                <div class="description-line d-flex mb-1">
                                    <input type="text" name="description_lines[]"
                                        class="form-control form-control-sm rounded-0 me-2" value="{{ $line->text }}">
                                    <button type="button" class="btn btn-sm btn-danger remove-line">Remove</button>
                                </div>
                            @endforeach
                            @if (!$product->descriptionLines->count())
                                <div class="description-line d-flex mb-1">
                                    <input type="text" name="description_lines[]"
                                        class="form-control form-control-sm rounded-0 me-2">
                                    <button type="button" class="btn btn-sm btn-danger remove-line">Remove</button>
                                </div>
                            @endif
                        </div>

                        <button type="button" id="addLine" class="btn btn-sm btn-outline-primary mb-3">Add
                            Line</button>

                        <h6 class="mb-2">Attributes</h6>
                        <div id="attributesSection" class="mb-3">
                            @foreach ($attributes as $attr)
                                <div class="mb-2 attribute-box" data-id="{{ $attr->id }}">
                                    <label class="small">{{ $attr->name }}</label>
                                    <div class="d-flex flex-wrap gap-2">
                                        @foreach ($attr->values as $val)
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input attribute-check" type="checkbox"
                                                    name="product_attributes[{{ $attr->id }}][]"
                                                    value="{{ $val->id }}"
                                                    @foreach ($product->variants as $v)
                            @if ($v->attributeValues->contains('id', $val->id)) checked @endif @endforeach>
                                                <label class="form-check-label">{{ $val->value }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach

                        </div>

                        <h6 class="mb-2">Variants</h6>
                        <div id="variantsSection" class="mb-2">
                            @foreach ($product->variants as $index => $variant)
                                <div class="variant-box mb-2 d-flex flex-column">
                                    <div class="d-flex gap-2 mb-1 align-items-center">
                                        <input type="text" name="variants[{{ $index }}][sku]"
                                            class="form-control form-control-sm" value="{{ $variant->sku }}"
                                            placeholder="SKU">
                                        <input type="number" name="variants[{{ $index }}][price]"
                                            class="form-control form-control-sm" value="{{ $variant->price }}"
                                            placeholder="Price">
                                        <button type="button"
                                            class="btn btn-sm btn-danger remove-variant">Remove</button>
                                    </div>
                                    @foreach ($variant->attributeValues as $attrValue)
                                        <input type="hidden" name="variants[{{ $index }}][attributes][]"
                                            value="{{ $attrValue->id }}">
                                    @endforeach
                                </div>
                            @endforeach

                        </div>
                        <button type="button" id="generateVariants" class="btn btn-sm btn-outline-warning">Generate
                            Variants</button>
                    </div>
                </div>

                <div class="mt-3 text-end">
                    <button class="btn btn-success">UPDATE PRODUCT</button>
                </div>
            </form>
        </x-widget>
    </div>
@endsection

@section('scripts')
    <script>
        $(function() {
            $('#addLine').click(function() {
                $('#descriptionLines').append(
                    `<input type="text" name="description_lines[]" class="form-control form-control-sm mb-1 rounded-0">`
                );
            });
            const oldImageUrl = "{{ $product->image_url }}";
            console.log('Old Image URL:', oldImageUrl);

            function showPreview(fileInput) {
                const file = fileInput.files[0];
                if (file) {
                    $('#imagePreview').attr('src', URL.createObjectURL(file)).show();
                } else if (oldImageUrl) {
                    $('#imagePreview').attr('src', oldImageUrl).show();
                } else {
                    $('#imagePreview').hide();
                }
            }

            // On file change
            $('input[name="image"]').on('change', function() {
                showPreview(this);
            });

            // Optional: if you have a "clear" button
            $('#clearImage').on('click', function() {
                const input = $('input[name="image"]');
                input.val(''); // clear input
                showPreview(input[0]);
            });

            // Run on page load just in case
            showPreview($('input[name="image"]')[0]);

            function cartesian(arr) {
                return arr.reduce((a, b) => a.flatMap(d => b.map(e => [...d, e])), [
                    []
                ]);
            }

            $('#generateVariants').click(function() {
                $('#variantsSection').empty();
                let selected = {};
                $('.attribute-box').each(function() {
                    let attrId = $(this).data('id');
                    let vals = [];
                    $(this).find('.attribute-check:checked').each(function() {
                        vals.push({
                            id: $(this).val(),
                            name: $(this).next('label').text()
                        });
                    });
                    if (vals.length) selected[attrId] = vals;
                });
                let keys = Object.keys(selected);
                if (!keys.length) return;
                let arrays = keys.map(k => selected[k]);
                let combos = cartesian(arrays);

                combos.forEach((combo, index) => {
                    let selectsHtml = combo.map(c =>
                        `<div>${c.name} <input type="hidden" name="variants[${index}][attributes][]" value="${c.id}"></div>`
                    ).join('');
                    $('#variantsSection').append(`
                <div class="mb-2">
                    <input type="text" name="variants[${index}][sku]" class="form-control form-control-sm mb-1 rounded-0" placeholder="SKU">
                    <input type="number" step="0.01" name="variants[${index}][price]" class="form-control form-control-sm mb-1 rounded-0" placeholder="Price">
                    ${selectsHtml}
                </div>
            `);
                });
            });

            $(document).on('click', '.remove-variant', function() {
                $(this).closest('.variant-box').remove();
            });


            $('#form_update_product').on('submit', function(e) {
                e.preventDefault();
                let formData = new FormData(this);
                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        if(res.data.success){
                            toastr.success(res.msg || 'Product updated!');
                        }else{
                            toastr.error(res.msg || 'failed');
                        }
                        setTimeout(() => window.location.href = res.location, 500);
                    },
                    error: function(err) {
                        console.log(err)
                    }
                });
            });
        });
    </script>
@endsection
