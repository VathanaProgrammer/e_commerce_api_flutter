@extends('layouts.app')

@section('content')
<div class="container py-4">
    <x-widget title="Edit User">
        <form id="userForm" method="POST" action="{{ route('users.update', $user->id) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row g-3 align-items-start">

                {{-- Profile Image --}}
                <div class="col-md-3 text-center">
                    <label class="form-label d-block">Profile Image</label>
                    <img id="profilePreview" 
                         src="{{ $user->profile_image_url ?? '/img/default-user.png' }}" 
                         class="img-fluid rounded-circle mb-2"
                         style="width:100%; aspect-ratio:1/1; object-fit:cover;">
                    <input type="file" name="profile_image_url" class="form-control form-control-sm rounded-0" accept="image/*">
                </div>

                {{-- Other User Inputs --}}
                <div class="col-md-9">
                    <div class="row g-3">

                        {{-- Prefix --}}
                        <div class="col-md-3">
                            <label>Prefix</label>
                            <select name="prefix" class="form-select form-select-sm rounded-0">
                                <option value="">—</option>
                                <option value="Mr" {{ $user->prefix == 'Mr' ? 'selected' : '' }}>Mr</option>
                                <option value="Miss" {{ $user->prefix == 'Miss' ? 'selected' : '' }}>Miss</option>
                                <option value="other" {{ $user->prefix == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>

                        {{-- First Name --}}
                        <div class="col-md-4">
                            <label>First Name</label>
                            <input name="first_name" class="form-control form-control-sm rounded-0" required value="{{ $user->first_name }}">
                        </div>

                        {{-- Last Name --}}
                        <div class="col-md-5">
                            <label>Last Name</label>
                            <input name="last_name" class="form-control form-control-sm rounded-0" value="{{ $user->last_name }}">
                        </div>

                        {{-- Email --}}
                        <div class="col-md-6">
                            <label>Email</label>
                            <input name="email" type="email" class="form-control form-control-sm rounded-0" required value="{{ $user->email }}">
                        </div>

                        {{-- Username --}}
                        <div class="col-md-6">
                            <label>Username</label>
                            <input name="username" class="form-control form-control-sm rounded-0" value="{{ $user->username }}">
                        </div>

                        {{-- Phone --}}
                        <div class="col-md-6">
                            <label>Phone</label>
                            <input name="phone" class="form-control form-control-sm rounded-0" value="{{ $user->phone }}">
                        </div>

                        {{-- City --}}
                        <div class="col-md-6">
                            <label>City</label>
                            <input name="city" class="form-control form-control-sm rounded-0" value="{{ $user->city }}">
                        </div>

                        {{-- Address --}}
                        <div class="col-md-12">
                            <label>Address</label>
                            <input name="address" class="form-control form-control-sm rounded-0" value="{{ $user->address }}">
                        </div>

                        {{-- Profile Completion --}}
                        <div class="col-md-12">
                            <label>Profile Completion (%)</label>
                            <input type="number" name="profile_completion" min="0" max="100" class="form-control form-control-sm rounded-0" value="{{ $user->profile_completion }}">
                        </div>
  
                        {{-- Role --}}
                        <div class="col-md-6">
                            <label>Role</label>
                            <select name="role" class="form-select form-select-sm rounded-0">
                                <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="staff" {{ $user->role == 'staff' ? 'selected' : '' }}>Staff</option>
                                <option value="customer" {{ $user->role == 'customer' ? 'selected' : '' }}>Customer</option>
                            </select>
                        </div>

                        {{-- Gender --}}
                        <div class="col-md-6">
                            <label>Gender</label>
                            <select name="gender" class="form-select form-select-sm rounded-0">
                                <option value="">—</option>
                                <option value="male" {{ $user->gender == 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ $user->gender == 'female' ? 'selected' : '' }}>Female</option>
                                <option value="other" {{ $user->gender == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>

                        {{-- Active --}}
                        <div class="col-md-12 mt-2">
                            <div class="form-check">
                                <input type="checkbox" name="is_active" class="form-check-input" {{ $user->is_active ? 'checked' : '' }}>
                                <label class="form-check-label">Active</label>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-sm btn-success rounded-0">Update</button>
                <a href="{{ route('users.index') }}" class="btn btn-sm btn-secondary rounded-0">Cancel</a>
            </div>
        </form>
    </x-widget>
</div>
@endsection

@section('scripts')
    <script>
        $(function() {
            // Add description line
            $('#addLine').click(function() {
                $('#descriptionLines').append(`
            <div class="description-line d-flex mb-1">
                <input type="text" name="description_lines[]" class="form-control form-control-sm rounded-0 me-2">
                <button type="button" class="btn btn-sm btn-danger remove-line">Remove</button>
            </div>
        `);
            });

            // Remove description line
            $(document).on('click', '.remove-line', function() {
                $(this).closest('.description-line').remove();
            });

            // Image preview
            const oldImageUrl = "{{ $product->image_url }}";

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

            $('input[name="image"]').on('change', function() {
                showPreview(this);
            });

            showPreview($('input[name="image"]')[0]);

            // Cartesian product helper
            function cartesian(arr) {
                return arr.reduce((a, b) => a.flatMap(d => b.map(e => [...d, e])), [
                    []
                ]);
            }

            // ✅ FIX: Get next variant index (preserve existing variants)
            function getNextVariantIndex() {
                let maxIndex = -1;
                $('#variantsSection .variant-box').each(function() {
                    // Extract index from name attribute like "variants[0][sku]"
                    const nameAttr = $(this).find('input[name*="variants"]').first().attr('name');
                    if (nameAttr) {
                        const match = nameAttr.match(/variants\[(\d+)\]/);
                        if (match) {
                            const idx = parseInt(match[1]);
                            if (idx > maxIndex) maxIndex = idx;
                        }
                    }
                });
                return maxIndex + 1;
            }

            // ✅ FIX: Check if variant already exists
            function variantExists(combo) {
                const comboIds = combo.map(c => c.id).sort().join(',');

                let exists = false;
                $('#variantsSection .variant-box').each(function() {
                    const variantIds = [];
                    $(this).find('input[name*="[attributes]"]').each(function() {
                        variantIds.push($(this).val());
                    });

                    if (variantIds.sort().join(',') === comboIds) {
                        exists = true;
                        return false; // break loop
                    }
                });

                return exists;
            }

            // Generate Variants button
            $('#generateVariants').click(function() {
                // ✅ DON'T clear existing variants
                // $('#variantsSection').empty(); // REMOVED THIS LINE

                // Get selected attributes
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
                if (!keys.length) {
                    toastr.warning('Please select at least one attribute');
                    return;
                }

                let arrays = keys.map(k => selected[k]);
                let combos = cartesian(arrays);

                // Get starting index for new variants
                let startIndex = getNextVariantIndex();
                let addedCount = 0;

                combos.forEach((combo) => {
                    // ✅ Skip if variant already exists
                    if (variantExists(combo)) {
                        console.log('Variant already exists, skipping:', combo.map(c => c.name)
                            .join(', '));
                        return;
                    }

                    const currentIndex = startIndex + addedCount;

                    let attributesHtml = combo.map(c =>
                        `<span class="badge bg-secondary me-1">${c.name}</span>
                <input type="hidden" name="variants[${currentIndex}][attributes][]" value="${c.id}">`
                    ).join('');

                    $('#variantsSection').append(`
                <div class="variant-box mb-2 d-flex flex-column">
                    <div class="d-flex gap-2 mb-1 align-items-center">
                        <input type="text" name="variants[${currentIndex}][sku]" 
                            class="form-control form-control-sm" placeholder="SKU">
                        <input type="number" step="0.01" name="variants[${currentIndex}][price]" 
                            class="form-control form-control-sm" placeholder="Price">
                        <button type="button" class="btn btn-sm btn-danger remove-variant">Remove</button>
                    </div>
                    <div class="mb-1">${attributesHtml}</div>
                </div>
            `);

                    addedCount++;
                });

                if (addedCount > 0) {
                    toastr.success(`${addedCount} new variant(s) added`);
                } else {
                    toastr.info('All variants already exist');
                }
            });

            // Remove variant
            $(document).on('click', '.remove-variant', function() {
                $(this).closest('.variant-box').remove();
            });

            // Form submission
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
                        if (res.success) {
                            toastr.success(res.msg || 'Product updated!');
                            setTimeout(() => window.location.href = res.location, 500);
                        } else {
                            toastr.error(res.msg || 'Failed to update product');
                        }
                    },
                    error: function(err) {
                        console.error(err);
                        toastr.error('An error occurred. Please try again.');
                    }
                });
            });
        });
    </script>
@endsection
