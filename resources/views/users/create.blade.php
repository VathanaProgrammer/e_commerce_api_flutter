@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <x-widget title="Add User">

            <form id="userForm" method="POST" action="{{ route('users.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="row g-3 align-items-start">

                    {{-- Profile Image --}}
                    <div class="col-md-3 text-center">
                        <label class="form-label d-block">Profile Image</label>
                        <img id="profilePreview" src="{{ $img ?? '/img/default-user.png' }}"
                            class="img-fluid rounded-circle mb-2" style="width:100%; aspect-ratio:1/1; object-fit:cover;">
                        <input type="file" name="profile_image_url" class="form-control form-control-sm rounded-0"
                            accept="image/*">
                    </div>

                    {{-- Other User Inputs --}}
                    <div class="col-md-9">
                        <div class="row g-3">

                            {{-- Prefix --}}
                            <div class="col-md-3">
                                <label>Prefix</label>
                                <select name="prefix" class="form-select form-select-sm rounded-0">
                                    <option value="">—</option>
                                    <option value="Mr">Mr</option>
                                    <option value="Miss">Miss</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>

                            {{-- First Name --}}
                            <div class="col-md-4">
                                <label>First Name</label>
                                <input name="first_name" class="form-control form-control-sm rounded-0" required>
                            </div>

                            {{-- Last Name --}}
                            <div class="col-md-5">
                                <label>Last Name</label>
                                <input name="last_name" class="form-control form-control-sm rounded-0">
                            </div>

                            {{-- Email --}}
                            <div class="col-md-6">
                                <label>Email</label>
                                <input name="email" type="email" class="form-control form-control-sm rounded-0"
                                    required>
                            </div>

                            {{-- Username --}}
                            <div class="col-md-6">
                                <label>Username</label>
                                <input name="username" class="form-control form-control-sm rounded-0">
                            </div>

                            {{-- Password --}}
                            <div class="col-md-6">
                                <label>Password</label>
                                <input name="password" type="password" class="form-control form-control-sm rounded-0"
                                    required>
                            </div>

                            {{-- Role --}}
                            <div class="col-md-6">
                                <label>Role</label>
                                <select name="role" class="form-select form-select-sm rounded-0">
                                    <option value="admin">Admin</option>
                                    <option value="staff">Staff</option>
                                    <option value="customer" selected>Customer</option>
                                </select>
                            </div>

                            {{-- Gender --}}
                            <div class="col-md-6">
                                <label>Gender</label>
                                <select name="gender" class="form-select form-select-sm rounded-0">
                                    <option value="">—</option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>

                            {{-- Phone --}}
                            <div class="col-md-6">
                                <label>Phone</label>
                                <input name="phone" class="form-control form-control-sm rounded-0">
                            </div>

                            {{-- City --}}
                            <div class="col-md-6">
                                <label>City</label>
                                <input name="city" class="form-control form-control-sm rounded-0">
                            </div>

                            {{-- Address --}}
                            <div class="col-md-12">
                                <label>Address</label>
                                <input name="address" class="form-control form-control-sm rounded-0">
                            </div>

                            {{-- Profile Completion --}}
                            <div class="col-md-12">
                                <label>Profile Completion (%)</label>
                                <input type="number" name="profile_completion" min="0" max="100" value="0"
                                    class="form-control form-control-sm rounded-0">
                            </div>

                            {{-- Active --}}
                            <div class="col-md-12 mt-2">
                                <div class="form-check">
                                    <input type="checkbox" name="is_active" class="form-check-input" checked>
                                    <label class="form-check-label">Active</label>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-sm btn-success rounded-0">Save</button>
                    <a href="{{ route('users.index') }}" class="btn btn-sm btn-secondary rounded-0">Cancel</a>
                </div>
            </form>

        </x-widget>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {

            // Preview profile image
            $('input[name="profile_image_url"]').on('change', function(e) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#profilePreview').attr('src', e.target.result);
                }
                reader.readAsDataURL(this.files[0]);
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
                        $('button[type="submit"]').prop('disabled', true).text('Saving...');
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
                        $('button[type="submit"]').prop('disabled', false).text('Save');
                    }
                });
            });

        });
    </script>
@endsection
