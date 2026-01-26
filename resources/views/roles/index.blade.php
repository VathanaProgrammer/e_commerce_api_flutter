@extends('layouts.app')

@section('title', 'Roles & Permissions')

@section('content')
<div class="container-fluid py-4 animate__animated animate__fadeIn">
    <div class="row mb-4 align-items-center">
        <div class="col-6">
            <h1 class="h3 mb-0 text-gray-800 fw-bold">Roles & Permissions</h1>
            <p class="text-muted small mb-0">Manage system access levels and granular permissions</p>
        </div>
        <div class="col-6 text-end">
            <a href="{{ route('roles.create') }}" class="btn btn-primary shadow-sm rounded-pill px-4">
                <i class="bi bi-plus-lg me-2"></i>New Role
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="rolesTable">
                    <thead class="bg-light text-secondary small text-uppercase fw-bold">
                        <tr>
                            <th class="px-4 py-3">Role Name</th>
                            <th class="px-4 py-3">Permissions</th>
                            <th class="px-4 py-3 text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="small">
                        <!-- Loaded via DataTables -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    $('#rolesTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('roles.data') }}",
        columns: [
            { data: 'name', name: 'name', className: 'fw-bold px-4' },
            { data: 'permissions', name: 'permissions', orderable: false, searchable: false, className: 'px-4' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-end px-4' }
        ],
        language: {
            processing: '<div class="spinner-border text-primary" role="status"></div>'
        },
        drawCallback: function() {
            $('.dataTables_paginate .paginate_button').addClass('btn btn-sm btn-outline-primary mx-1');
        }
    });
});
</script>
@endsection
