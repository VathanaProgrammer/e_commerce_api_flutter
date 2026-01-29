@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <x-widget title="Edit My Profile">
                <form id="userEditForm" method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row g-4">
                        <!-- Left: Avatar -->
                        <div class="col-md-4 text-center">
                            <div class="profile-avatar-wrapper position-relative mx-auto mb-3" style="width: 150px; height: 150px;">
                                <img id="profilePreview" 
                                     src="{{ auth()->user()->profile_image_url ?? '/img/default-user.png' }}" 
                                     class="rounded-circle shadow-sm w-100 h-100 object-fit-cover border border-3 border-light">
                                <label for="profile_image_input" class="position-absolute bottom-0 end-0 bg-white shadow-sm rounded-circle p-2 cursor-pointer hover-scale" style="width: 40px; height: 40px;">
                                    <i class="bi bi-camera text-primary"></i>
                                </label>
                                <input type="file" id="profile_image_input" name="profile_image" class="d-none" accept="image/*">
                            </div>
                            <p class="text-muted small">Allowed: jpg, jpeg, png, webp (Max 2MB)</p>
                        </div>

                        <!-- Right: Form Fields -->
                        <div class="col-md-8">
                            <div class="row g-3">
                                
                                {{-- Name Fields --}}
                                <div class="col-md-4">
                                    <label class="form-label small text-uppercase text-muted fw-bold">Prefix</label>
                                    <select name="prefix" class="form-select form-select-lg bg-light border-0">
                                        <option value="">—</option>
                                        <option value="Mr" {{ $user->prefix == 'Mr' ? 'selected' : '' }}>Mr</option>
                                        <option value="Miss" {{ $user->prefix == 'Miss' ? 'selected' : '' }}>Miss</option>
                                        <option value="other" {{ $user->prefix == 'other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                </div>
                                <div class="col-md-8"></div> {{-- Spacer --}}

                                <div class="col-md-6">
                                    <label class="form-label small text-uppercase text-muted fw-bold">First Name</label>
                                    <input name="first_name" class="form-control form-control-lg bg-light border-0" required value="{{ $user->first_name }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small text-uppercase text-muted fw-bold">Last Name</label>
                                    <input name="last_name" class="form-control form-control-lg bg-light border-0" value="{{ $user->last_name }}">
                                </div>

                                {{-- Contact --}}
                                <div class="col-12">
                                    <label class="form-label small text-uppercase text-muted fw-bold">Email Address</label>
                                    <input name="email" type="email" class="form-control form-control-lg bg-light border-0" required value="{{ $user->email }}">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label small text-uppercase text-muted fw-bold">Phone</label>
                                    <input name="phone" class="form-control form-control-lg bg-light border-0" value="{{ $user->phone }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small text-uppercase text-muted fw-bold">Username</label>
                                    <input name="username" class="form-control form-control-lg bg-light border-0" value="{{ $user->username }}">
                                </div>

                                {{-- Address --}}
                                <div class="col-md-4">
                                    <label class="form-label small text-uppercase text-muted fw-bold">City</label>
                                    <input name="city" class="form-control form-control-lg bg-light border-0" value="{{ $user->city }}">
                                </div>
                                <div class="col-md-8">
                                    <label class="form-label small text-uppercase text-muted fw-bold">Full Address</label>
                                    <input name="address" class="form-control form-control-lg bg-light border-0" value="{{ $user->address }}">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label small text-uppercase text-muted fw-bold">Gender</label>
                                    <select name="gender" class="form-select form-select-lg bg-light border-0">
                                        <option value="">—</option>
                                        <option value="male" {{ $user->gender == 'male' ? 'selected' : '' }}>Male</option>
                                        <option value="female" {{ $user->gender == 'female' ? 'selected' : '' }}>Female</option>
                                        <option value="other" {{ $user->gender == 'other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-5 pt-3 border-top">
                        <a href="{{ route('users.profile', auth()->id()) }}" class="btn btn-light btn-lg px-4">Cancel</a>
                        <button type="submit" class="btn btn-primary btn-lg px-5 shadow-sm">Save Changes</button>
                    </div>
                </form>
            </x-widget>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {

            // Preview profile image
            $('#profile_image_input').on('change', function(e) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#profilePreview').attr('src', e.target.result);
                }
                reader.readAsDataURL(this.files[0]);
            });

            // AJAX form submit
            $('#userEditForm').on('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(this);

                
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
                        $('button[type="submit"]').prop('disabled', true).text('Updating...');
                    },
                    success: function(res) {
                        if (res.success) {
                            toastr.success(res.msg || 'User updated successfully!');
                            window.location.href = res.location;
                        } else {
                            toastr.error(res.msg || 'Failed to update user.');
                        }
                    },
                    error: function(xhr) {
                        toastr.error('Error: ' + (xhr.responseJSON.message ||
                            'Something went wrong.'));
                    },
                    complete: function() {
                        $('button[type="submit"]').prop('disabled', false).text('Update');
                    }
                });
            });

        });
    </script>
@endsection
