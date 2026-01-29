@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <x-widget title="Add User">

            <form id="userForm" method="POST" action="{{ route('users.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="row g-4 align-items-start">

                    {{-- Profile Image --}}
                    <div class="col-md-3 text-center">
                        <div class="profile-upload-section p-4 rounded-4" style="background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%); animation: fadeInLeft 0.5s ease forwards;">
                            <label class="form-label d-block fw-semibold mb-3">Profile Image</label>
                            <div class="profile-image-wrapper position-relative mx-auto mb-3" style="width: 150px; height: 150px;">
                                <img id="profilePreview" src="{{ $img ?? '/img/default-user.png' }}"
                                    class="img-fluid rounded-circle shadow" 
                                    style="width:100%; height:100%; object-fit:cover; border: 4px solid white; transition: all 0.3s ease;">
                                <div class="profile-overlay position-absolute top-0 start-0 w-100 h-100 rounded-circle d-flex align-items-center justify-content-center" 
                                     style="background: rgba(0,0,0,0.5); opacity: 0; transition: all 0.3s ease; cursor: pointer;">
                                    <i class="bi bi-camera text-white fs-3"></i>
                                </div>
                            </div>
                            <input type="file" name="profile_image" class="form-control form-control-sm rounded-pill"
                                accept="image/*" id="profileInput">
                            <small class="text-muted mt-2 d-block">JPG, PNG or GIF (Max 2MB)</small>
                        </div>
                    </div>

                    {{-- Other User Inputs --}}
                    <div class="col-md-9">
                        <div class="row g-3">

                            {{-- Prefix --}}
                            <div class="col-md-3 form-group-animate" style="animation-delay: 0.1s;">
                                <label class="form-label fw-semibold">Prefix</label>
                                <select name="prefix" class="form-select rounded-3">
                                    <option value="">—</option>
                                    <option value="Mr">Mr</option>
                                    <option value="Miss">Miss</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>

                            {{-- First Name --}}
                            <div class="col-md-4 form-group-animate" style="animation-delay: 0.15s;">
                                <label class="form-label fw-semibold">First Name <span class="text-danger">*</span></label>
                                <input name="first_name" class="form-control rounded-3" placeholder="Enter first name" required>
                            </div>

                            {{-- Last Name --}}
                            <div class="col-md-5 form-group-animate" style="animation-delay: 0.2s;">
                                <label class="form-label fw-semibold">Last Name</label>
                                <input name="last_name" class="form-control rounded-3" placeholder="Enter last name">
                            </div>

                            {{-- Email --}}
                            <div class="col-md-6 form-group-animate" style="animation-delay: 0.25s;">
                                <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 rounded-start-3"><i class="bi bi-envelope text-muted"></i></span>
                                    <input name="email" type="email" class="form-control border-start-0 rounded-end-3" placeholder="email@example.com" required>
                                </div>
                            </div>

                            {{-- Username --}}
                            <div class="col-md-6 form-group-animate" style="animation-delay: 0.3s;">
                                <label class="form-label fw-semibold">Username</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 rounded-start-3"><i class="bi bi-at text-muted"></i></span>
                                    <input name="username" class="form-control border-start-0 rounded-end-3" placeholder="username">
                                </div>
                            </div>

                            {{-- Password --}}
                            <div class="col-md-6 form-group-animate" style="animation-delay: 0.35s;">
                                <label class="form-label fw-semibold">Password <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 rounded-start-3"><i class="bi bi-lock text-muted"></i></span>
                                    <input name="password" type="password" class="form-control border-start-0 rounded-end-3" placeholder="Enter password" required>
                                </div>
                            </div>

                            {{-- Role --}}
                            <div class="col-md-6 form-group-animate" style="animation-delay: 0.4s;">
                                <label class="form-label fw-semibold">Role</label>
                                <select name="role" class="form-select rounded-3">
                                    <option value="admin">Admin</option>
                                    <option value="staff">Staff</option>
                                    <option value="customer" selected>Customer</option>
                                </select>
                            </div>

                            {{-- Gender --}}
                            <div class="col-md-6 form-group-animate" style="animation-delay: 0.45s;">
                                <label class="form-label fw-semibold">Gender</label>
                                <select name="gender" class="form-select rounded-3">
                                    <option value="">—</option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>

                            {{-- Phone --}}
                            <div class="col-md-6 form-group-animate" style="animation-delay: 0.5s;">
                                <label class="form-label fw-semibold">Phone</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 rounded-start-3"><i class="bi bi-telephone text-muted"></i></span>
                                    <input name="phone" class="form-control border-start-0 rounded-end-3" placeholder="+1 234 567 8900">
                                </div>
                            </div>

                            {{-- City --}}
                            <div class="col-md-6 form-group-animate" style="animation-delay: 0.55s;">
                                <label class="form-label fw-semibold">City</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 rounded-start-3"><i class="bi bi-geo-alt text-muted"></i></span>
                                    <input name="city" class="form-control border-start-0 rounded-end-3" placeholder="City name">
                                </div>
                            </div>

                            {{-- Address --}}
                            <div class="col-md-12 form-group-animate" style="animation-delay: 0.6s;">
                                <label class="form-label fw-semibold">Address</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 rounded-start-3"><i class="bi bi-house text-muted"></i></span>
                                    <input name="address" class="form-control border-start-0 rounded-end-3" placeholder="Full address">
                                </div>
                            </div>

                            {{-- Profile Completion --}}
                            <div class="col-md-12 form-group-animate" style="animation-delay: 0.65s;">
                                <label class="form-label fw-semibold">Profile Completion (%)</label>
                                <input type="range" name="profile_completion" min="0" max="100" value="0"
                                    class="form-range" id="profileRange">
                                <div class="d-flex justify-content-between">
                                    <small class="text-muted">0%</small>
                                    <small class="text-primary fw-bold" id="rangeValue">0%</small>
                                    <small class="text-muted">100%</small>
                                </div>
                            </div>

                            {{-- Active --}}
                            <div class="col-md-12 mt-2 form-group-animate" style="animation-delay: 0.7s;">
                                <div class="form-check form-switch">
                                    <input type="checkbox" name="is_active" class="form-check-input" id="isActive" checked style="width: 3em; height: 1.5em;">
                                    <label class="form-check-label fw-semibold ms-2" for="isActive">Active User</label>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="mt-4 d-flex gap-2 justify-content-end" style="animation: fadeInUp 0.5s ease 0.8s forwards; opacity: 0;">
                    <a href="{{ route('users.index') }}" class="btn btn-light rounded-pill px-4">
                        <i class="bi bi-x-lg me-2"></i>Cancel
                    </a>
                    <button type="submit" class="btn btn-success rounded-pill px-4 shadow-sm">
                        <i class="bi bi-check-lg me-2"></i>Save User
                    </button>
                </div>
            </form>

        </x-widget>
    </div>

    <style>
        .profile-image-wrapper:hover .profile-overlay {
            opacity: 1 !important;
        }
        
        .profile-image-wrapper:hover img {
            transform: scale(1.05);
        }

        .form-group-animate {
            opacity: 0;
            animation: fadeInUp 0.4s ease forwards;
        }

        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
            from {
                opacity: 0;
                transform: translateY(15px);
            }
        }

        .input-group-text {
            border: 2px solid #e2e8f0;
            border-right: none;
        }

        .input-group .form-control {
            border: 2px solid #e2e8f0;
            border-left: none;
        }

        .input-group .form-control:focus {
            border-color: #667eea;
            box-shadow: none;
        }

        .input-group:focus-within .input-group-text {
            border-color: #667eea;
        }
    </style>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {

            // Preview profile image
            $('input[name="profile_image"]').on('change', function(e) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#profilePreview').attr('src', e.target.result);
                }
                reader.readAsDataURL(this.files[0]);
            });

            // Profile completion range
            $('#profileRange').on('input', function() {
                $('#rangeValue').text($(this).val() + '%');
            });

            // Click on overlay to trigger file input
            $('.profile-overlay').on('click', function() {
                $('#profileInput').click();
            });

            // AJAX form submit
            $('#userForm').on('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(this);

                // Convert checkbox to true/false
                formData.set('is_active', $('input[name="is_active"]').is(':checked') ? 1 : 0);

                $.ajax({
                    url: $(this).attr('action'),
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('input[name="_token"]').val()
                    },
                    beforeSend: function() {
                        $('button[type="submit"]').prop('disabled', true).html('<i class="bi bi-arrow-repeat me-2" style="animation: spin 1s linear infinite;"></i>Saving...');
                    },
                    success: function(res) {
                        toastr.success(res.msg || 'User added successfully!');
                        window.location.href = res.location;
                        $('#userForm')[0].reset();
                        $('#profilePreview').attr('src', '/img/default-user.png');
                    },
                    error: function(xhr) {
                        toastr.error('Error: ' + (xhr.responseJSON?.message ||
                            'Something went wrong.'));
                    },
                    complete: function() {
                        $('button[type="submit"]').prop('disabled', false).html('<i class="bi bi-check-lg me-2"></i>Save User');
                    }
                });
            });

        });
    </script>
@endsection
