@extends('layouts.app')

@section('title', 'Edit Role')

@section('content')
<div class="container py-5 animate__animated animate__fadeIn">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="card-header bg-primary py-4 px-5">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h4 class="text-white fw-bold mb-1">Edit Role: {{ $role->name }}</h4>
                            <p class="text-white-50 small mb-0">Modify role name or update assigned permissions</p>
                        </div>
                        <i class="bi bi-shield-lock text-white-50 fs-2"></i>
                    </div>
                </div>
                <div class="card-body p-5">
                    <form id="editRoleForm">
                        @csrf
                        @method('PUT')
                        <div class="mb-4">
                            <label class="form-label fw-semibold text-secondary small text-uppercase">Role Name</label>
                            <input type="text" name="name" value="{{ $role->name }}" class="form-control form-control-lg border-2" required>
                        </div>

                        <div class="mb-5">
                            <label class="form-label fw-semibold text-secondary small text-uppercase d-block mb-3">Permissions</label>
                            <div class="row g-3">
                                @foreach($permissions->groupBy(function($item) { 
                                    return explode(' ', $item->name)[count(explode(' ', $item->name)) - 1]; 
                                }) as $group => $groupPermissions)
                                <div class="col-md-6 mb-4">
                                    <div class="p-3 rounded-3 bg-light border shadow-sm h-100">
                                        <h6 class="fw-bold text-dark text-capitalize mb-3 border-bottom pb-2">
                                            <i class="bi bi-folder2-open me-2 text-primary"></i>{{ $group }}
                                        </h6>
                                        @foreach($groupPermissions as $permission)
                                        <div class="form-check custom-checkbox mb-2">
                                            <input class="form-check-input border-2" type="checkbox" name="permissions[]" 
                                                   value="{{ $permission->id }}" 
                                                   id="perm_{{ $permission->id }}"
                                                   {{ in_array($permission->id, $rolePermissions) ? 'checked' : '' }}>
                                            <label class="form-check-label small" for="perm_{{ $permission->id }}">
                                                {{ $permission->name }}
                                            </label>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary btn-lg rounded-pill px-5 shadow">
                                <span class="spinner-border spinner-border-sm d-none me-2" role="status"></span>
                                Update Role
                            </button>
                            <a href="{{ route('roles.index') }}" class="btn btn-light btn-lg rounded-pill px-4">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.form-control:focus {
    border-color: #6366f1;
    box-shadow: 0 0 0 0.25rem rgba(99, 102, 241, 0.1);
}
.custom-checkbox .form-check-input:checked {
    background-color: #6366f1;
    border-color: #6366f1;
}
</style>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    $('#editRoleForm').on('submit', function(e) {
        e.preventDefault();
        const $btn = $(this).find('button[type="submit"]');
        const $spinner = $btn.find('.spinner-border');

        $btn.prop('disabled', true);
        $spinner.removeClass('d-none');

        $.ajax({
            url: "{{ route('roles.update', $role->id) }}",
            type: "POST",
            data: $(this).serialize(),
            success: function(res) {
                if (res.success) {
                    toastr.success(res.msg);
                    setTimeout(() => window.location.href = res.location, 1000);
                }
            },
            error: function(err) {
                $btn.prop('disabled', false);
                $spinner.addClass('d-none');
                const errors = err.responseJSON.errors;
                if (errors) {
                    Object.values(errors).forEach(e => toastr.error(e[0]));
                } else {
                    toastr.error('Something went wrong. Please try again.');
                }
            }
        });
    });
});
</script>
@endsection
