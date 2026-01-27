@extends('layouts.app')

@section('title', 'Edit Role')

@push('styles')
<style>
    :root {
        --premium-primary: #667eea;
        --premium-secondary: #764ba2;
        --premium-border: #e2e8f0;
        --premium-text: #1e293b;
    }

    .premium-card {
        background: #ffffff;
        border: 1px solid var(--premium-border);
        border-radius: 20px;
        overflow: hidden;
        transition: all 0.3s ease;
    }

    .card-header-premium {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        padding: 16px 24px;
        font-weight: 700;
        color: var(--premium-text);
        border-bottom: 1px solid var(--premium-border);
        display: flex;
        align-items: center;
    }

    .form-label-premium {
        font-weight: 600;
        font-size: 0.8rem;
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

    .input-group-premium .form-control {
        padding-left: 42px;
        border: 2px solid var(--premium-border);
        border-radius: 12px;
        padding-top: 10px;
        padding-bottom: 10px;
        font-weight: 500;
    }

    .permission-group-card {
        background: #fff;
        border: 1px solid var(--premium-border);
        border-radius: 16px;
        padding: 20px;
        height: 100%;
        transition: all 0.2s;
    }

    .permission-group-card:hover {
        border-color: var(--premium-primary);
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    }

    .group-title {
        font-weight: 700;
        color: var(--premium-text);
        border-bottom: 2px solid #f1f5f9;
        margin-bottom: 15px;
        padding-bottom: 8px;
        text-transform: capitalize;
        display: flex;
        align-items: center;
    }

    .group-title i {
        color: var(--premium-primary);
        margin-right: 10px;
    }

    .custom-checkbox {
        display: flex;
        align-items: center;
        padding: 8px 12px;
        border-radius: 10px;
        transition: all 0.2s;
        cursor: pointer;
        margin-bottom: 4px;
    }

    .custom-checkbox:hover {
        background: #f8fafc;
    }

    .btn-submit {
        background: linear-gradient(135deg, var(--premium-primary) 0%, var(--premium-secondary) 100%);
        color: white;
        border: none;
        border-radius: 14px;
        padding: 12px 30px;
        font-weight: 700;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        transition: all 0.3s;
    }

    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        color: white;
    }
</style>
@endpush

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <x-widget title="Edit Role">
                <form id="editRoleForm">
                    @csrf
                    @method('PUT')
                    
                    <div class="premium-card mb-4">
                        <div class="card-header-premium">
                            <i class="bi bi-pencil-square me-2 text-primary"></i> Role Details
                        </div>
                        <div class="p-4">
                            <div class="mb-2">
                                <label class="form-label-premium">Role Name <span class="text-danger">*</span></label>
                                <div class="input-group-premium">
                                    <i class="bi bi-shield-lock input-icon"></i>
                                    <input type="text" name="name" value="{{ $role->name }}" class="form-control" placeholder="Role name" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <h5 class="fw-bold text-dark mb-3 ps-2">Update Authorized Permissions</h5>
                    
                    <div class="row g-4 mb-4">
                        @php
                            $groupIcons = [
                                'sales' => 'bi-receipt',
                                'products' => 'bi-box-seam',
                                'categories' => 'bi-tags',
                                'attributes' => 'bi-sliders',
                                'users' => 'bi-people',
                                'settings' => 'bi-gear'
                            ];
                        @endphp

                        @foreach($permissions->groupBy(function($item) { 
                            return explode(' ', $item->name)[count(explode(' ', $item->name)) - 1]; 
                        }) as $group => $groupPermissions)
                        <div class="col-md-6 col-xl-4">
                            <div class="permission-group-card">
                                <div class="group-title">
                                    <i class="bi {{ $groupIcons[strtolower($group)] ?? 'bi-folder2' }}"></i>
                                    {{ $group }}
                                </div>
                                @foreach($groupPermissions as $permission)
                                <label class="custom-checkbox">
                                    <input type="checkbox" name="permissions[]" value="{{ $permission->id }}" 
                                           class="form-check-input"
                                           {{ in_array($permission->id, $rolePermissions) ? 'checked' : '' }}>
                                    <span class="small fw-semibold {{ in_array($permission->id, $rolePermissions) ? 'text-primary' : 'text-secondary' }}">
                                        {{ ucwords($permission->name) }}
                                    </span>
                                </label>
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded-4">
                        <a href="{{ route('roles.index') }}" class="btn btn-link text-muted fw-bold text-decoration-none px-4">
                            <i class="bi bi-arrow-left me-2"></i>Cancel
                        </a>
                        <button type="submit" class="btn-submit">
                            <span class="spinner-border spinner-border-sm d-none me-2" role="status"></span>
                            <i class="bi bi-shield-check me-2"></i>Update Role Permissions
                        </button>
                    </div>
                </form>
            </x-widget>
        </div>
    </div>
</div>
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
                    setTimeout(() => window.location.href = res.location, 800);
                }
            },
            error: function(err) {
                $btn.prop('disabled', false);
                $spinner.addClass('d-none');
                const errors = err.responseJSON.errors;
                if(errors) {
                    Object.values(errors).forEach(e => toastr.error(e[0]));
                } else {
                    toastr.error('An unexpected error occurred during update.');
                }
            }
        });
    });
});
</script>
@endsection
