@extends('layouts.app')

@section('content')
<div class="container py-4">
    <x-widget title="Edit My Profile">
        <form id="userEditForm" method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
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
                    <input type="file" name="profile_image" class="form-control form-control-sm rounded-0" accept="image/*">
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

                    </div>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-sm btn-success rounded-0">Update Profile</button>
                <a href="{{ route('users.profile', auth()->id()) }}" class="btn btn-sm btn-secondary rounded-0">Cancel</a>
            </div>
        </form>
    </x-widget>
</div>
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

            // AJAX form submit
            $('#userEditForm').on('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(this);

                // Convert checkbox to true/false (if any exist)
                // formData.set('is_active', $('input[name="is_active"]').is(':checked') ? 1 : 0);
                
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
