@extends('layouts.app')

@section('content')
    <div class="container-fluid py-4">
        <x-widget title="Create Product">
            <form id="form_add_product" enctype="multipart/form-data">
                @csrf

                <div class="row g-4">

                    {{-- Left Column --}}
                    <div class="col-lg-6">
                        {{-- Basic Information --}}
                        <div class="card shadow-sm mb-4">
                            <div class="card-header bg-primary text-white">
                                <h6 class="mb-0"><i class="fas fa-info-circle"></i> Basic Information</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Product Name <span class="text-danger">*</span></label>
                                            <input type="text" name="name" class="form-control" placeholder="Enter product name" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold d-block">Status</label>
                                        <div class="form-check form-switch">
                                            <input type="checkbox" name="active" class="form-check-input" id="active" value="1" checked>
                                            <label class="form-check-label" for="active">Active</label>
                                        </div>
                                        <div class="form-check form-switch">
                                            <input type="checkbox" name="is_featured" class="form-check-input" id="isFeatured" value="1">
                                            <label class="form-check-label" for="isFeatured">Featured</label>
                                        </div>
                                        <div class="form-check form-switch">
                                            <input type="checkbox" name="is_recommended" class="form-check-input" id="isRecommended" value="1">
                                            <label class="form-check-label" for="isRecommended">Recommended</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Category <span class="text-danger">*</span></label>
                                    <select name="category_id" class="form-select" required>
                                        <option value="">-- Select Category --</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-0">
                                    <label class="form-label fw-bold">Product Image</label>
                                    <input type="file" name="image" class="form-control" accept="image/*">
                                    <div class="mt-3 text-center">
                                        <img id="imagePreview" src="" class="img-thumbnail" style="max-height:200px; display:none;">
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Discount Section --}}
                        <div class="card shadow-sm">
                            <div class="card-header bg-success text-white">
                                <h6 class="mb-0"><i class="fas fa-percentage"></i> Discount (Optional)</h6>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">Discount Name</label>
                                        <input type="text" name="discount[name]" class="form-control" placeholder="e.g. Summer Sale">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-bold">Value</label>
                                        <input type="number" step="0.01" name="discount[value]" class="form-control" placeholder="0.00">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-bold">Type</label>
                                        <select name="discount[is_percentage]" class="form-select">
                                            <option value="1">Percentage (%)</option>
                                            <option value="0">Fixed ($)</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2 d-flex align-items-end">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="discount[active]" value="1" checked>
                                            <label class="form-check-label">Active</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Right Column --}}
                    <div class="col-lg-6">
                        {{-- Description --}}
                        <div class="card shadow-sm mb-4">
                            <div class="card-header bg-info text-white">
                                <h6 class="mb-0"><i class="fas fa-align-left"></i> Product Description</h6>
                            </div>
                            <div class="card-body">
                                <div id="descriptionLines" class="mb-2">
                                    <div class="description-line mb-2">
                                        <div class="input-group">
                                            <input type="text" name="description_lines[]" class="form-control" placeholder="Description line">
                                            <button type="button" class="btn btn-danger remove-line"><i class="fas fa-times"></i></button>
                                        </div>
                                    </div>
                                </div>
                                <button type="button" id="addLine" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-plus"></i> Add Line
                                </button>
                            </div>
                        </div>

                        {{-- Attributes --}}
                        <div class="card shadow-sm mb-4">
                            <div class="card-header bg-warning text-dark">
                                <h6 class="mb-0"><i class="fas fa-tags"></i> Product Attributes</h6>
                            </div>
                            <div class="card-body">
                                <div id="attributesSection">
                                    @foreach ($attributes as $attr)
                                        <div class="attribute-box mb-3 p-3 border rounded" data-id="{{ $attr->id }}">
                                            <label class="fw-bold text-primary mb-2">{{ $attr->name }}</label>
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
                        </div>

                        {{-- Variants --}}
                        <div class="card shadow-sm">
                            <div class="card-header bg-secondary text-white">
                                <h6 class="mb-0"><i class="fas fa-boxes"></i> Product Variants</h6>
                            </div>
                            <div class="card-body">
                                <div id="variantsSection" class="mb-3"></div>
                                <button type="button" id="generateVariants" class="btn btn-warning">
                                    <i class="fas fa-magic"></i> Generate Variants
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Submit Button --}}
                <div class="mt-4 text-end">
                    <a href="{{ route('products.index') }}" class="btn btn-secondary me-2">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-success btn-lg">
                        <i class="fas fa-save"></i> SAVE PRODUCT
                    </button>
                </div>
            </form>
        </x-widget>
    </div>

    <style>
        .card {
            border: none;
        }
        
        .card-header {
            border-bottom: 2px solid rgba(0,0,0,0.1);
        }
        
        .attribute-box {
            background: #f8f9fa;
        }
        
        .variant-item {
            background: #fff;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            padding: 15px;
            margin-bottom: 10px;
        }
        
        .variant-attrs {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin-top: 8px;
        }
        
        .variant-badge {
            background: #e7f3ff;
            border: 1px solid #b3d9ff;
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 0.85rem;
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
                        <div class="input-group">
                            <input type="text" name="description_lines[]" class="form-control" placeholder="Description line">
                            <button type="button" class="btn btn-danger remove-line"><i class="fas fa-times"></i></button>
                        </div>
                    </div>
                `);
            });

            // Remove description line
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

            // Cartesian product helper
            function cartesian(arr) {
                return arr.reduce((a, b) => a.flatMap(d => b.map(e => [...d, e])), [[]]);
            }

            // Generate variants
            $('#generateVariants').click(function() {
                $('#variantsSection').empty();

                let selected = {};
                $('.attribute-box').each(function() {
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
                    toastr.warning('Please select at least one attribute value');
                    return;
                }

                let arrays = keys.map(k => selected[k]);
                let combos = cartesian(arrays);

                combos.forEach((combo, index) => {
                    let attributeBadges = combo.map(c =>
                        `<span class="variant-badge">${c.attrName}: ${c.name}</span>`
                    ).join('');
                    
                    let hiddenInputs = combo.map(c =>
                        `<input type="hidden" name="variants[${index}][attributes][]" value="${c.id}">`
                    ).join('');

                    $('#variantsSection').append(`
                        <div class="variant-item">
                            <div class="row g-2">
                                <div class="col-md-5">
                                    <label class="form-label fw-bold small">SKU</label>
                                    <input type="text" name="variants[${index}][sku]" class="form-control form-control-sm" placeholder="SKU">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold small">Price</label>
                                    <input type="number" step="0.01" name="variants[${index}][price]" class="form-control form-control-sm" placeholder="0.00">
                                </div>
                                <div class="col-md-3 d-flex align-items-end">
                                    <button type="button" class="btn btn-sm btn-danger remove-variant w-100">
                                        <i class="fas fa-trash"></i> Remove
                                    </button>
                                </div>
                            </div>
                            <div class="variant-attrs">${attributeBadges}</div>
                            ${hiddenInputs}
                        </div>
                    `);
                });
                
                toastr.success(`${combos.length} variant(s) generated!`);
            });

            // Remove variant
            $(document).on('click', '.remove-variant', function() {
                $(this).closest('.variant-item').remove();
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
                        toastr.success(res.msg || 'Product created successfully!');
                        window.location.href = res.location;
                    },
                    error: function(err) {
                        toastr.error('Failed to create product. Please check all fields.');
                    }
                });
            });
        });
    </script>
@endsection