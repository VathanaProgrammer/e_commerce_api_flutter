@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-11">
                <x-widget title="Create New Product">
                    <form id="form_add_product" enctype="multipart/form-data">
                        @csrf

                        <div class="row g-4">
                            {{-- Left Column: Core Details --}}
                            <div class="col-lg-7">
                                {{-- Basic Info Card --}}
                                <div class="premium-card mb-4 section-box">
                                    <div class="card-header-premium">
                                        <i class="bi bi-info-circle-fill me-2"></i>
                                        <span>General Information</span>
                                    </div>
                                    <div class="p-4">
                                        <div class="mb-4">
                                            <label class="form-label-premium">Product Name <span class="text-danger">*</span></label>
                                            <div class="input-group-premium">
                                                <i class="bi bi-tag input-icon"></i>
                                                <input type="text" name="name" class="form-control" placeholder="Enter product name" required>
                                            </div>
                                        </div>

                                        <div class="mb-4">
                                            <label class="form-label-premium">Category <span class="text-danger">*</span></label>
                                            <div class="input-group-premium">
                                                <i class="bi bi-grid-fill input-icon"></i>
                                                <select name="category_id" class="form-select" required>
                                                    <option value="">Select Category</option>
                                                    @foreach ($categories as $category)
                                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="row g-3">
                                            <div class="col-sm-4">
                                                <div class="status-toggle-card">
                                                    <div class="form-check form-switch p-0 m-0 d-flex align-items-center justify-content-between w-100">
                                                        <label class="form-check-label fw-bold m-0" for="active">
                                                            <i class="bi bi-check-circle me-1 text-success"></i> Active
                                                        </label>
                                                        <input type="checkbox" name="active" class="form-check-input" id="active" value="1" checked>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="status-toggle-card">
                                                    <div class="form-check form-switch p-0 m-0 d-flex align-items-center justify-content-between w-100">
                                                        <label class="form-check-label fw-bold m-0" for="isFeatured">
                                                            <i class="bi bi-star-fill me-1 text-warning"></i> Featured
                                                        </label>
                                                        <input type="checkbox" name="is_featured" class="form-check-input" id="isFeatured" value="1">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="status-toggle-card">
                                                    <div class="form-check form-switch p-0 m-0 d-flex align-items-center justify-content-between w-100">
                                                        <label class="form-check-label fw-bold m-0" for="isRecommended">
                                                            <i class="bi bi-hand-thumbs-up-fill me-1 text-info"></i> Recommend
                                                        </label>
                                                        <input type="checkbox" name="is_recommended" class="form-check-input" id="isRecommended" value="1">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Description Card --}}
                                <div class="premium-card mb-4 section-box">
                                    <div class="card-header-premium">
                                        <i class="bi bi-text-left me-2"></i>
                                        <span>Detailed Description</span>
                                    </div>
                                    <div class="p-4">
                                        <div id="descriptionLines" class="mb-3">
                                            <div class="description-line mb-2">
                                                <div class="input-group-premium">
                                                    <i class="bi bi-dash input-icon"></i>
                                                    <input type="text" name="description_lines[]" class="form-control" placeholder="E.g. 100% Organic Cotton">
                                                    <button type="button" class="btn-remove-line remove-line">
                                                        <i class="bi bi-x"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <button type="button" id="addLine" class="btn btn-premium-secondary btn-sm">
                                            <i class="bi bi-plus-lg me-1"></i> Add Feature Line
                                        </button>
                                    </div>
                                </div>

                                {{-- Image Card --}}
                                <div class="premium-card section-box">
                                    <div class="card-header-premium">
                                        <i class="bi bi-image me-2"></i>
                                        <span>Product Visuals</span>
                                    </div>
                                    <div class="p-4">
                                        <div class="image-upload-wrapper">
                                            <label class="upload-zone" for="productImage">
                                                <div class="upload-icon">
                                                    <i class="bi bi-cloud-arrow-up-fill"></i>
                                                </div>
                                                <p class="m-0">Click or drag image here (PNG, JPG, max 2MB)</p>
                                                <input type="file" name="image" id="productImage" class="d-none" accept="image/*">
                                            </label>
                                            <div class="image-preview-area text-center d-none" id="previewContainer">
                                                <img id="imagePreview" src="" class="img-fluid rounded-4 shadow-sm">
                                                <button type="button" class="btn btn-sm btn-danger mt-3 rounded-pill" id="removePreview">
                                                    <i class="bi bi-trash3 me-1"></i> Remove Image
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Right Column: Attributes & Variants --}}
                            <div class="col-lg-5">
                                {{-- Attributes Card --}}
                                <div class="premium-card mb-4 section-box">
                                    <div class="card-header-premium">
                                        <i class="bi bi-sliders me-2"></i>
                                        <span>Dynamic Attributes</span>
                                    </div>
                                    <div class="p-4">
                                        <div id="attributesSection">
                                            @foreach ($attributes as $attr)
                                                <div class="attribute-pill-group mb-4" data-id="{{ $attr->id }}">
                                                    <label class="form-label-premium mb-2">{{ $attr->name }}</label>
                                                    <div class="attribute-options">
                                                        @foreach ($attr->values as $val)
                                                            <div class="attr-checkbox">
                                                                <input class="attribute-check" type="checkbox"
                                                                    name="product_attributes[{{ $attr->id }}][]"
                                                                    value="{{ $val->id }}"
                                                                    id="attr_{{ $attr->id }}_{{ $val->id }}">
                                                                <label class="attr-label" for="attr_{{ $attr->id }}_{{ $val->id }}">
                                                                    {{ $val->value }}
                                                                </label>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                        <div class="alert alert-info border-0 rounded-4 p-3 small">
                                            <i class="bi bi-info-circle-fill me-2"></i>
                                            Select attributes above, then click 'Generate Variants' below to create product combinations.
                                        </div>
                                        <button type="button" id="generateVariants" class="btn btn-premium-primary w-100">
                                            <i class="bi bi-magic me-2"></i> Generate Combinations
                                        </button>
                                    </div>
                                </div>

                                {{-- Variants Card --}}
                                <div class="premium-card mb-4 section-box">
                                    <div class="card-header-premium">
                                        <i class="bi bi-stack me-2"></i>
                                        <span>Inventory Variants</span>
                                    </div>
                                    <div class="p-4">
                                        <div id="variantsSection" class="variants-container">
                                            <div class="text-center py-4 text-muted" id="noVariantsPlaceholder">
                                                <i class="bi bi-layers fs-1 opacity-25 mb-2 d-block"></i>
                                                <p class="small">No variants generated yet.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Discount Card --}}
                                <div class="premium-card section-box">
                                    <div class="card-header-premium">
                                        <i class="bi bi-percent me-2"></i>
                                        <span>Promotion Details</span>
                                    </div>
                                    <div class="p-4">
                                        <div class="mb-3">
                                            <label class="form-label-premium">Discount Title</label>
                                            <div class="input-group-premium">
                                                <i class="bi bi-pencil-square input-icon"></i>
                                                <input type="text" name="discount[name]" class="form-control" placeholder="Summer Sale">
                                            </div>
                                        </div>
                                        <div class="row g-3 align-items-end">
                                            <div class="col-8">
                                                <label class="form-label-premium">Discount Value</label>
                                                <div class="input-group-premium">
                                                    <i class="bi bi-cash-stack input-icon"></i>
                                                    <input type="number" step="0.01" name="discount[value]" class="form-control" placeholder="0.00">
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <select name="discount[is_percentage]" class="form-select border-2 py-2">
                                                    <option value="1">Percentage (%)</option>
                                                    <option value="0">Fixed ($)</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="mt-3 text-end d-flex align-items-center justify-content-end">
                                            <span class="small me-2 text-muted">Active?</span>
                                            <div class="form-check form-switch m-0">
                                                <input class="form-check-input" type="checkbox" name="discount[active]" value="1" checked>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Final Submit --}}
                        <div class="mt-5 pt-4 border-top d-flex justify-content-end gap-3 align-items-center">
                            <a href="{{ route('products.index') }}" class="btn btn-light-muted rounded-pill px-4 py-2">
                                <i class="bi bi-x-lg me-1"></i> Cancel
                            </a>
                            <button type="submit" id="btnSubmitProduct" class="btn btn-premium-action rounded-pill px-5 py-2 fw-bold">
                                <i class="bi bi-check-lg me-1"></i> Finalize & Save Product
                            </button>
                        </div>
                    </form>
                </x-widget>
            </div>
        </div>
    </div>

    <style>
        :root {
            --premium-primary: #667eea;
            --premium-secondary: #764ba2;
            --premium-bg: #f8fafc;
            --premium-card-bg: #ffffff;
            --premium-border: #e2e8f0;
            --premium-text: #1e293b;
        }

        .premium-card {
            background: var(--premium-card-bg);
            border: 1px solid var(--premium-border);
            border-radius: 20px;
            overflow: hidden;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .premium-card:hover {
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            border-color: #cbd5e1;
            transform: translateY(-2px);
        }

        .card-header-premium {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            padding: 14px 24px;
            font-weight: 700;
            color: var(--premium-text);
            border-bottom: 1px solid var(--premium-border);
            display: flex;
            align-items: center;
        }

        .card-header-premium i {
            color: var(--premium-primary);
        }

        .form-label-premium {
            font-weight: 600;
            font-size: 0.85rem;
            color: #64748b;
            margin-bottom: 8px;
            display: block;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .input-group-premium {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-icon {
            position: absolute;
            left: 14px;
            color: #94a3b8;
            font-size: 1.1rem;
            z-index: 10;
        }

        .input-group-premium .form-control,
        .input-group-premium .form-select {
            padding-left: 42px;
            border: 2px solid var(--premium-border);
            border-radius: 14px;
            padding-top: 10px;
            padding-bottom: 10px;
            font-weight: 500;
        }

        .input-group-premium .form-control:focus {
            border-color: var(--premium-primary);
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        }

        .btn-remove-line {
            position: absolute;
            right: 8px;
            background: none;
            border: none;
            color: #f43f5e;
            font-size: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0;
            opacity: 0.6;
            transition: all 0.2s;
            z-index: 11;
        }

        .btn-remove-line:hover {
            opacity: 1;
            transform: scale(1.2);
        }

        .status-toggle-card {
            background: #fff;
            border: 2px solid var(--premium-border);
            border-radius: 14px;
            padding: 10px 16px;
            transition: all 0.2s;
        }

        .status-toggle-card:hover {
            border-color: var(--premium-primary);
            background: #fdfefe;
        }

        .image-upload-wrapper {
            border: 2px dashed #cbd5e1;
            border-radius: 20px;
            padding: 10px;
            transition: all 0.3s;
            cursor: pointer;
            min-height: 180px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .image-upload-wrapper:hover {
            border-color: var(--premium-primary);
            background: rgba(102, 126, 234, 0.02);
        }

        .upload-zone {
            text-align: center;
            width: 100%;
            height: 100%;
            padding: 40px;
            cursor: pointer;
        }

        .upload-icon {
            font-size: 3rem;
            color: var(--premium-primary);
            margin-bottom: 10px;
            transition: transform 0.3s;
        }

        .image-upload-wrapper:hover .upload-icon {
            transform: translateY(-5px);
        }

        .attribute-options {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .attr-checkbox {
            position: relative;
        }

        .attr-checkbox input {
            position: absolute;
            opacity: 0;
            cursor: pointer;
        }

        .attr-label {
            background: white;
            border: 2px solid var(--premium-border);
            padding: 8px 18px;
            border-radius: 30px;
            cursor: pointer;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.2s;
            user-select: none;
        }

        .attr-checkbox input:checked + .attr-label {
            background: var(--premium-primary);
            border-color: var(--premium-primary);
            color: white;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }

        .attr-label:hover {
            border-color: #cbd5e1;
        }

        .variant-premium-card {
            background: white;
            border: 2px solid var(--premium-border);
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 16px;
            transition: all 0.3s;
        }

        .variant-premium-card:hover {
            border-color: var(--premium-primary);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }

        .variant-badge-pill {
            display: inline-block;
            background: #f1f5f9;
            color: #475569;
            font-weight: 700;
            font-size: 0.75rem;
            padding: 5px 12px;
            border-radius: 8px;
            margin-right: 6px;
            margin-bottom: 6px;
            text-transform: uppercase;
            border: 1px solid #e2e8f0;
        }

        .btn-premium-primary {
            background: var(--premium-primary);
            color: white;
            border-radius: 14px;
            padding: 12px;
            font-weight: 700;
            border: none;
            transition: all 0.3s;
        }

        .btn-premium-primary:hover {
            background: var(--premium-secondary);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .btn-premium-secondary {
            background: #f1f5f9;
            color: var(--premium-primary);
            border-radius: 14px;
            padding: 8px 20px;
            font-weight: 700;
            border: none;
            transition: all 0.3s;
        }

        .btn-premium-secondary:hover {
            background: #e2e8f0;
            color: var(--premium-secondary);
        }

        .btn-premium-action {
            background: linear-gradient(135deg, var(--premium-primary) 0%, var(--premium-secondary) 100%);
            color: white;
            border: none;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
            transition: all 0.3s;
        }

        .btn-premium-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.5);
            color: white;
        }

        .btn-light-muted {
            background: #f1f5f9;
            color: #64748b;
            border: none;
        }

        .btn-light-muted:hover {
            background: #e2e8f0;
            color: #1e293b;
        }

        /* Group animations */
        .section-box { opacity: 0; transform: translateY(20px); animation: fadeInUp 0.5s ease forwards; }
        .section-box:nth-child(1) { animation-delay: 0.1s; }
        .section-box:nth-child(2) { animation-delay: 0.2s; }
        .section-box:nth-child(3) { animation-delay: 0.3s; }

        @keyframes fadeInUp {
            to { opacity: 1; transform: translateY(0); }
        }

        .input-group-premium .form-control::placeholder {
            color: #cbd5e1;
            font-weight: 400;
        }
    </style>
@endsection

@section('scripts')
    <script>
        $(function() {
            // Add description line
            $('#addLine').click(function() {
                const $newLine = $(`
                    <div class="description-line mb-2" style="display:none">
                        <div class="input-group-premium">
                            <i class="bi bi-dash input-icon"></i>
                            <input type="text" name="description_lines[]" class="form-control" placeholder="Description feature line">
                            <button type="button" class="btn-remove-line remove-line">
                                <i class="bi bi-x"></i>
                            </button>
                        </div>
                    </div>
                `);
                $('#descriptionLines').append($newLine);
                $newLine.slideDown(200);
            });

            $(document).on('click', '.remove-line', function() {
                const $line = $(this).closest('.description-line');
                $line.slideUp(200, function() { $(this).remove(); });
            });

            // Image handling
            const $productImage = $('#productImage');
            const $previewContainer = $('#previewContainer');
            const $uploadZone = $('.upload-zone');
            const $imagePreview = $('#imagePreview');

            $productImage.on('change', function(e) {
                const [file] = e.target.files;
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(re) {
                        $imagePreview.attr('src', re.target.result);
                        $uploadZone.addClass('d-none');
                        $previewContainer.removeClass('d-none').addClass('animate__animated animate__fadeIn');
                    }
                    reader.readAsDataURL(file);
                }
            });

            $('#removePreview').click(function() {
                $productImage.val('');
                $previewContainer.addClass('d-none');
                $uploadZone.removeClass('d-none');
            });

            function cartesian(arr) {
                return arr.reduce((a, b) => a.flatMap(d => b.map(e => [...d, e])), [[]]);
            }

            // Generate variants
            $('#generateVariants').click(function() {
                let selected = {};
                $('.attribute-pill-group').each(function() {
                    let attrId = $(this).data('id');
                    let attrName = $(this).find('.form-label-premium').first().text(); // Changed selector
                    let vals = [];
                    $(this).find('.attribute-check:checked').each(function() {
                        vals.push({
                            id: $(this).val(),
                            name: $(this).next('.attr-label').text(), // Changed selector
                            attrName: attrName
                        });
                    });
                    if (vals.length) selected[attrId] = vals;
                });

                let keys = Object.keys(selected);
                if (!keys.length) {
                    toastr.warning('Please select at least one attribute first!');
                    return;
                }

                $('#noVariantsPlaceholder').hide();
                $('#variantsSection').html(''); // Clear existing

                let arrays = keys.map(k => selected[k]);
                let combos = cartesian(arrays);

                combos.forEach((combo, index) => {
                    let badges = combo.map(c => `<span class="variant-badge-pill">${c.attrName}: ${c.name}</span>`).join('');
                    let hiddenInputs = combo.map(c => `<input type="hidden" name="variants[${index}][attributes][]" value="${c.id}">`).join('');

                    $('#variantsSection').append(`
                        <div class="variant-premium-card animate__animated animate__fadeInUp">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="variant-badges-container">${badges}</div>
                                <button type="button" class="btn btn-sm text-danger remove-variant p-0"><i class="bi bi-trash3"></i></button>
                            </div>
                            <div class="row g-3">
                                <div class="col-6">
                                    <label class="form-label-premium">Variant SKU</label>
                                    <div class="input-group-premium">
                                        <i class="bi bi-barcode input-icon"></i>
                                        <input type="text" name="variants[${index}][sku]" class="form-control form-control-sm" placeholder="SKU-PRD-${index}">
                                    </div>
                                </div>
                                <div class="col-6">
                                    <label class="form-label-premium">Base Price ($)</label>
                                    <div class="input-group-premium">
                                        <i class="bi bi-currency-dollar input-icon"></i>
                                        <input type="number" step="0.01" name="variants[${index}][price]" class="form-control form-control-sm" placeholder="0.00" required>
                                    </div>
                                </div>
                            </div>
                            ${hiddenInputs}
                        </div>
                    `);
                });
                
                toastr.success(`${combos.length} potential variants generated!`);
            });

            $(document).on('click', '.remove-variant', function() {
                $(this).closest('.variant-premium-card').remove();
                if ($('#variantsSection .variant-premium-card').length === 0) {
                    $('#noVariantsPlaceholder').show();
                }
            });

            // Form submission
            $('#form_add_product').on('submit', function(e) {
                e.preventDefault();
                
                const variantCount = $('#variantsSection .variant-premium-card').length;
                if (variantCount === 0) {
                    toastr.error('Please generate at least one variant combination.');
                    return;
                }

                const $btn = $('#btnSubmitProduct');
                const originalText = $btn.html();
                $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Processing...');
                
                let formData = new FormData(this);
                
                $.ajax({
                    url: "{{ route('products.store') }}",
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        toastr.success(res.msg || 'Great! Product has been created.');
                        window.location.href = res.location;
                    },
                    error: function(err) {
                        $btn.prop('disabled', false).html(originalText);
                        if(err.status === 422) {
                            const errors = err.responseJSON.errors;
                            Object.keys(errors).forEach(key => toastr.error(errors[key][0]));
                        } else {
                            toastr.error('Internal server error while creating product.');
                        }
                    }
                });
            });
        });
    </script>
@endsection