@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <x-widget title="Create Product">
            <form id="form_add_product" enctype="multipart/form-data">
                @csrf

                <div class="row g-4">
                    {{-- Left Column --}}
                    <div class="col-md-6">
                        {{-- Basic Info --}}
                        <div class="section-box mb-4">
                            <h6 class="section-title">Basic Information</h6>
                            
                            <div class="mb-3">
                                <label class="form-label">Product Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" placeholder="Enter product name" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Category <span class="text-danger">*</span></label>
                                <select name="category_id" class="form-select" required>
                                    <option value="">Select Category</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="row">
                                <div class="col-4">
                                    <div class="form-check form-switch">
                                        <input type="checkbox" name="active" class="form-check-input" id="active" value="1" checked>
                                        <label class="form-check-label" for="active">Active</label>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-check form-switch">
                                        <input type="checkbox" name="is_featured" class="form-check-input" id="isFeatured" value="1">
                                        <label class="form-check-label" for="isFeatured">Featured</label>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-check form-switch">
                                        <input type="checkbox" name="is_recommended" class="form-check-input" id="isRecommended" value="1">
                                        <label class="form-check-label" for="isRecommended">Recommended</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Image --}}
                        <div class="section-box mb-4">
                            <h6 class="section-title">Product Image</h6>
                            <input type="file" name="image" class="form-control mb-3" accept="image/*">
                            <div class="text-center">
                                <img id="imagePreview" src="" class="img-thumbnail" style="max-height:200px; display:none;">
                            </div>
                        </div>

                        {{-- Discount --}}
                        <div class="section-box">
                            <h6 class="section-title">Discount (Optional)</h6>
                            <div class="row g-2">
                                <div class="col-md-5">
                                    <input type="text" name="discount[name]" class="form-control" placeholder="Discount Name">
                                </div>
                                <div class="col-md-3">
                                    <input type="number" step="0.01" name="discount[value]" class="form-control" placeholder="Value">
                                </div>
                                <div class="col-md-2">
                                    <select name="discount[is_percentage]" class="form-select">
                                        <option value="1">%</option>
                                        <option value="0">$</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-check form-switch mt-2">
                                        <input class="form-check-input" type="checkbox" name="discount[active]" value="1" checked>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Right Column --}}
                    <div class="col-md-6">
                        {{-- Description --}}
                        <div class="section-box mb-4">
                            <h6 class="section-title">Description</h6>
                            <div id="descriptionLines" class="mb-2">
                                <div class="description-line mb-2">
                                    <div class="input-group input-group-sm">
                                        <input type="text" name="description_lines[]" class="form-control" placeholder="Description line">
                                        <button type="button" class="btn btn-outline-danger remove-line">×</button>
                                    </div>
                                </div>
                            </div>
                            <button type="button" id="addLine" class="btn btn-sm btn-outline-primary">+ Add Line</button>
                        </div>

                        {{-- Attributes --}}
                        <div class="section-box mb-4">
                            <h6 class="section-title">Attributes</h6>
                            <div id="attributesSection">
                                @foreach ($attributes as $attr)
                                    <div class="attribute-group mb-3" data-id="{{ $attr->id }}">
                                        <label class="fw-semibold mb-2">{{ $attr->name }}</label>
                                        <div class="d-flex flex-wrap gap-2">
                                            @foreach ($attr->values as $val)
                                                <div class="form-check">
                                                    <input class="form-check-input attribute-check" type="checkbox"
                                                        name="product_attributes[{{ $attr->id }}][]"
                                                        value="{{ $val->id }}"
                                                        id="attr_{{ $attr->id }}_{{ $val->id }}">
                                                    <label class="form-check-label" for="attr_{{ $attr->id }}_{{ $val->id }}">
                                                        {{ $val->value }}
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Variants --}}
                        <div class="section-box">
                            <h6 class="section-title">Variants</h6>
                            <div id="variantsSection" class="mb-3"></div>
                            <button type="button" id="generateVariants" class="btn btn-sm btn-outline-success">
                                Generate Variants
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Submit --}}
                <div class="mt-4 d-flex justify-content-end gap-2">
                    <a href="{{ route('products.index') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Save Product</button>
                </div>
            </form>
        </x-widget>
    </div>

    <style>
        .section-box {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            border: 1px solid #e0e0e0;
        }
        
        .section-title {
            font-weight: 600;
            margin-bottom: 15px;
            color: #333;
            border-bottom: 2px solid #dee2e6;
            padding-bottom: 8px;
        }

        .attribute-group {
            padding: 12px;
            background: white;
            border-radius: 6px;
            border: 1px solid #e0e0e0;
        }

        .variant-card {
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            padding: 15px;
            margin-bottom: 10px;
        }

        .variant-badges {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin-top: 10px;
        }

        .attr-badge {
            background: #e3f2fd;
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 0.85rem;
            border: 1px solid #90caf9;
        }
    </style>
@endsection

@section('scripts')
    <script>
        $(function() {
            // Add description line
            $('#addLine').click(function() {
                $('#descriptionLines').append(`
                    <div class="description-line mb-2">
                        <div class="input-group input-group-sm">
                            <input type="text" name="description_lines[]" class="form-control" placeholder="Description line">
                            <button type="button" class="btn btn-outline-danger remove-line">×</button>
                        </div>
                    </div>
                `);
            });

            $(document).on('click', '.remove-line', function() {
                $(this).closest('.description-line').remove();
            });

            // Image preview
            $('input[name="image"]').on('change', function(e) {
                const [file] = e.target.files;
                if (file) {
                    $('#imagePreview').attr('src', URL.createObjectURL(file)).show();
                }
            });

            function cartesian(arr) {
                return arr.reduce((a, b) => a.flatMap(d => b.map(e => [...d, e])), [[]]);
            }

            // Generate variants
            $('#generateVariants').click(function() {
                $('#variantsSection').empty();

                let selected = {};
                $('.attribute-group').each(function() {
                    let attrId = $(this).data('id');
                    let attrName = $(this).find('label').first().text();
                    let vals = [];
                    $(this).find('.attribute-check:checked').each(function() {
                        vals.push({
                            id: $(this).val(),
                            name: $(this).next('label').text(),
                            attrName: attrName
                        });
                    });
                    if (vals.length) selected[attrId] = vals;
                });

                let keys = Object.keys(selected);
                if (!keys.length) {
                    alert('Please select at least one attribute');
                    return;
                }

                let arrays = keys.map(k => selected[k]);
                let combos = cartesian(arrays);

                combos.forEach((combo, index) => {
                    let badges = combo.map(c => `<span class="attr-badge">${c.attrName}: ${c.name}</span>`).join('');
                    let hiddenInputs = combo.map(c => `<input type="hidden" name="variants[${index}][attributes][]" value="${c.id}">`).join('');

                    $('#variantsSection').append(`
                        <div class="variant-card">
                            <div class="row g-2 mb-2">
                                <div class="col-5">
                                    <input type="text" name="variants[${index}][sku]" class="form-control form-control-sm" placeholder="SKU">
                                </div>
                                <div class="col-4">
                                    <input type="number" step="0.01" name="variants[${index}][price]" class="form-control form-control-sm" placeholder="Price">
                                </div>
                                <div class="col-3">
                                    <button type="button" class="btn btn-sm btn-outline-danger remove-variant w-100">Remove</button>
                                </div>
                            </div>
                            <div class="variant-badges">${badges}</div>
                            ${hiddenInputs}
                        </div>
                    `);
                });
            });

            $(document).on('click', '.remove-variant', function() {
                $(this).closest('.variant-card').remove();
            });

            // Form submission
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
                        window.location.href = res.location;
                    },
                    error: function(err) {
                        toastr.error('Failed to create product');
                    }
                });
            });
        });
    </script>
@endsection