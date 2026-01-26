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
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border-radius: 16px;
            padding: 24px;
            border: 1px solid #e2e8f0;
            transition: all 0.3s ease;
            animation: fadeInUp 0.5s ease forwards;
            opacity: 0;
        }

        .section-box:nth-child(1) { animation-delay: 0.1s; }
        .section-box:nth-child(2) { animation-delay: 0.2s; }
        .section-box:nth-child(3) { animation-delay: 0.3s; }

        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
            from {
                opacity: 0;
                transform: translateY(20px);
            }
        }

        .section-box:hover {
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06);
            border-color: #cbd5e1;
        }
        
        .section-title {
            font-weight: 700;
            margin-bottom: 20px;
            color: #1e293b;
            border-bottom: 3px solid #667eea;
            padding-bottom: 10px;
            display: inline-block;
        }

        .attribute-group {
            padding: 16px;
            background: white;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            transition: all 0.3s ease;
        }

        .attribute-group:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            border-color: #667eea;
        }

        .variant-card {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 18px;
            margin-bottom: 12px;
            transition: all 0.3s ease;
            animation: slideIn 0.3s ease forwards;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(-10px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .variant-card:hover {
            border-color: #667eea;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.1);
        }

        .variant-badges {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 12px;
        }

        .attr-badge {
            background: linear-gradient(135deg, #eef2ff 0%, #e0e7ff 100%);
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.85rem;
            border: 1px solid #c7d2fe;
            color: #4338ca;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .attr-badge:hover {
            transform: scale(1.05);
            background: linear-gradient(135deg, #e0e7ff 0%, #c7d2fe 100%);
        }

        .form-control, .form-select {
            border-radius: 10px;
            border: 2px solid #e2e8f0;
            padding: 10px 14px;
            transition: all 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        }

        .form-check-input:checked {
            background-color: #667eea;
            border-color: #667eea;
        }

        #imagePreview {
            border-radius: 12px;
            transition: all 0.3s ease;
        }

        #imagePreview:hover {
            transform: scale(1.02);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
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