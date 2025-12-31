@extends('layouts.app')

@section('content')
<x-widget title="Edit User">

    <form id="userEditForm" method="POST" action="{{ route('users.update', $user->id) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row g-3 align-items-start">

            {{-- Profile Image --}}
            <div class="col-md-3 text-center">
                <label class="form-label d-block">Profile Image</label>
                <img id="profilePreview" 
                     src="{{ $user->profile_image_url }}" 
                     class="img-fluid rounded-circle mb-2"
                     style="width:100%; aspect-ratio:1/1; object-fit:cover;">
                <input type="file" 
                       name="profile_image" 
                       class="form-control form-control-sm rounded-0" 
                       accept="image/*">
            </div>

            {{-- Other Inputs --}}
            <div class="col-md-9">
                <div class="row g-3">
                    {{-- Prefix --}}
                    <div class="col-md-3">
                        <label>Prefix</label>
                        <select name="prefix" class="form-select form-select-sm rounded-0">
                            <option value="">—</option>
                            <option value="Mr" @selected($user->prefix === 'Mr')>Mr</option>
                            <option value="Miss" @selected($user->prefix === 'Miss')>Miss</option>
                            <option value="other" @selected($user->prefix === 'other')>Other</option>
                        </select>
                    </div>

                    {{-- First Name --}}
                    <div class="col-md-4">
                        <label>First Name</label>
                        <input name="first_name" value="{{ $user->first_name }}" class="form-control form-control-sm rounded-0" required>
                    </div>

                    {{-- Last Name --}}
                    <div class="col-md-5">
                        <label>Last Name</label>
                        <input name="last_name" value="{{ $user->last_name ?? '' }}" class="form-control form-control-sm rounded-0">
                    </div>

                    {{-- Email --}}
                    <div class="col-md-6">
                        <label>Email</label>
                        <input name="email" value="{{ $user->email }}" type="email" class="form-control form-control-sm rounded-0" required>
                    </div>

                    {{-- Username --}}
                    <div class="col-md-6">
                        <label>Username</label>
                        <input name="username" value="{{ $user->username }}" class="form-control form-control-sm rounded-0">
                    </div>

                    {{-- Password --}}
                    <div class="col-md-6">
                        <label>New Password (optional)</label>
                        <input name="password" type="password" class="form-control form-control-sm rounded-0">
                    </div>

                    {{-- Role --}}
                    <div class="col-md-6">
                        <label>Role</label>
                        <select name="role" class="form-select form-select-sm rounded-0">
                            @foreach(['admin','staff','customer'] as $r)
                                <option value="{{ $r }}" @selected($user->role === $r)>{{ ucfirst($r) }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Gender --}}
                    <div class="col-md-6">
                        <label>Gender</label>
                        <select name="gender" class="form-select form-select-sm rounded-0">
                            <option value="">—</option>
                            @foreach(['male','female','other'] as $g)
                                <option value="{{ $g }}" @selected($user->gender === $g)>{{ ucfirst($g) }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Active --}}
                    <div class="col-md-12 mt-2">
                        <div class="form-check">
                            <input type="checkbox" name="is_active" class="form-check-input" @checked($user->is_active)>
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
                $('button[type="submit"]').prop('disabled', true).text('Updating...');
            },
            success: function(res) {
                if(res.success){
                    toastr.success(res.msg || 'User updated successfully!');
                    window.location.href = res.location;
                }else{
                    toastr.error(res.msg || 'Failed to update user.');
                }
            },
            error: function(xhr) {
                toastr.error('Error: ' + (xhr.responseJSON.message || 'Something went wrong.'));
            },
            complete: function() {
                $('button[type="submit"]').prop('disabled', false).text('Update');
            }
        });
    });

});
</script>
@endsection
