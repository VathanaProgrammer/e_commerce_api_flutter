@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <x-widget title="Create Product">
            <form id="form_add_product" enctype="multipart/form-data">
                @csrf

                <div class="row g-4">

                    {{-- Left Column: Basic Info + Image --}}
                    <div class="col-md-6">
                        <div class="card p-3 mb-3">
                            <h6>Basic Info</h6>
                            <div class="d-flex justify-content-between">
                                <div class="mb-3">
                                    <label class="form-label small">Product Name</label>
                                    <input type="text" name="name" class="form-control form-control-sm rounded-0">
                                </div>
                                <div class="mb-3">
                                    <div class="mb- form-check">
                                        <input type="checkbox" name="is_recommended" class="form-check-input"
                                            id="isRecommended" value="1"
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
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small">Product Image</label>
                                <input type="file" name="image" class="form-control form-control-sm rounded-0"
                                    accept="image/*">
                                <img id="imagePreview" src="" class="img-fluid mt-2"
                                    style="max-height:150px; display:none;">
                            </div>
                        </div>

                        {{-- Discount --}}
                        <div class="card p-3">
                            <h6>Discount</h6>
                            <div class="row g-2 align-items-center">
                                <div class="col-auto">
                                    <input type="text" name="discount[name]"
                                        class="form-control form-control-sm rounded-0" placeholder="Discount Name">
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
                        </div>
                    </div>

                    {{-- Right Column: Description + Attributes + Variants --}}
                    <div class="col-md-6">
                        {{-- Description --}}
                        <div class="card p-3 mb-3">
                            <h6>Descriptions</h6>
                            <div id="descriptionLines" class="mb-2">
                                <div class="description-line d-flex mb-1">
                                    <input type="text" name="description_lines[]"
                                        class="form-control form-control-sm rounded-0 me-2">
                                    <button type="button" class="btn btn-sm btn-danger remove-line">Remove</button>
                                </div>
                            </div>
                            <button type="button" id="addLine" class="btn btn-sm btn-outline-primary">Add Line</button>

                        </div>

                        {{-- Attributes --}}
                        <div class="card p-3 mb-3">
                            <h6>Attributes</h6>
                            <div id="attributesSection">
                                @foreach ($attributes as $attr)
                                    <div class="attribute-box border p-2 mb-2" data-id="{{ $attr->id }}">
                                        <label class="small">{{ $attr->name }}</label>
                                        <div class="d-flex flex-wrap gap-2">
                                            @foreach ($attr->values as $val)
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input attribute-check" type="checkbox"
                                                        name="product_attributes[{{ $attr->id }}][]"
                                                        value="{{ $val->id }}">
                                                    <label class="form-check-label">{{ $val->value }}</label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Variants --}}
                        <div class="card p-3">
                            <h6>Variants</h6>
                            <div id="variantsSection"></div>
                            <button type="button" id="generateVariants"
                                class="btn btn-sm btn-outline-warning mt-2">Generate Variants</button>
                        </div>
                    </div>
                </div>

                {{-- Submit --}}
                <div class="mt-3 text-end">
                    <button class="btn btn-success">SAVE PRODUCT</button>
                </div>
            </form>
        </x-widget>
    </div>
@endsection

@section('scripts')
    <script>
        $(function() {
            $('#addLine').click(function() {
                $('#descriptionLines').append(`
                                                <div class="description-line d-flex mb-1">
                                                    <input type="text" name="description_lines[]" class="form-control form-control-sm rounded-0 me-2">
                                                    <button type="button" class="btn btn-sm btn-danger remove-line">Remove</button>
                                                </div>
                                            `);
            });

            // Remove a description line
            $(document).on('click', '.remove-line', function() {
                $(this).closest('.description-line').remove();
            });


            // Image Preview
            $('input[name="image"]').on('change', function(e) {
                const [file] = e.target.files;
                if (file) {
                    $('#imagePreview').attr('src', URL.createObjectURL(file)).show();
                }
            });

            function cartesian(arr) {
                return arr.reduce((a, b) => a.flatMap(d => b.map(e => [...d, e])), [
                    []
                ]);
            }

            $('#generateVariants').click(function() {
                $('#variantsSection').empty(); // clear old variants

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
            <div class="variant-box border p-2 mb-2 d-flex flex-column">
                <div class="d-flex gap-2 mb-1 align-items-center">
                    <input type="text" name="variants[${index}][sku]" class="form-control form-control-sm" placeholder="SKU">
                    <input type="number" name="variants[${index}][price]" class="form-control form-control-sm" placeholder="Price">
                    <button type="button" class="btn btn-sm btn-danger remove-variant">Remove</button>
                </div>
                ${selectsHtml}
            </div>
        `);
                });
            });


            $(document).on('click', '.remove-variant', function() {
                $(this).closest('.variant-box').remove();
            });


            $('#form_add_product').on('submit', function(e) {
                e.preventDefault();
                let formData = new FormData(this);
                $.ajax({
                    url: "{{ route('products.store') }}",
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        toastr.success(res.msg || 'Product created!');
                        form[0].reset();
                        $('#variantsSection').empty();
                        $('#imagePreview').hide();
                        window.location.href = res.location;
                    },
                    error: function(err) {
                        toastr.error('Failed.');
                    }
                });
            });
        });
    </script>
@endsection
