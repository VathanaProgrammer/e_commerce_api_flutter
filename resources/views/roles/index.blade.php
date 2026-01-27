@extends('layouts.app')

@section('title', 'Roles & Permissions')

@section('content')
<div class="container py-4">
    <x-widget title="Roles & Permissions">
        <div class="mb-4 d-flex justify-content-between align-items-center">
            <a href="{{ route('roles.create') }}" class="btn btn-success rounded-pill px-4 shadow-sm hover-lift">
                <i class="bi bi-shield-plus me-2"></i> Create Role
            </a>
            <div class="d-flex gap-2">
                <span class="badge bg-light text-dark rounded-pill px-3 py-2">
                    <i class="bi bi-shield-check me-1"></i> Access Control
                </span>
            </div>
        </div>

        <div class="card border-0 shadow-none">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover display nowrap w-full" id="rolesTable">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Role Name</th>
                                <th>Assigned Permissions</th>
                                <th class="text-center pe-4">Actions</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </x-widget>
</div>

<style>
    #rolesTable thead th {
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        border: none;
        padding: 15px 12px;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
        color: #475569;
    }

    #rolesTable tbody tr {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border-bottom: 1px solid #f1f5f9;
    }

    #rolesTable tbody tr:hover {
        background-color: rgba(102, 126, 234, 0.05) !important;
        transform: scale(1.002);
    }

    #rolesTable tbody td {
        padding: 14px 12px;
        vertical-align: middle;
    }

    .permission-badge {
        background: #f1f5f9;
        color: #475569;
        font-weight: 600;
        font-size: 0.75rem;
        padding: 4px 12px;
        border-radius: 6px;
        margin-right: 4px;
        margin-bottom: 4px;
        display: inline-block;
        border: 1px solid #e2e8f0;
    }
</style>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    $('#rolesTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('roles.data') }}",
        columns: [
            { 
                data: 'name', 
                name: 'name', 
                className: 'fw-bold ps-4'
            },
            { 
                data: 'permissions', 
                name: 'permissions', 
                orderable: false, 
                searchable: false,
                render: function(data) {
                    return `<div class="d-flex flex-wrap" style="max-width: 600px">${data}</div>`;
                }
            },
            { 
                data: 'actions', 
                name: 'actions', 
                orderable: false, 
                searchable: false, 
                className: 'text-center pe-4' 
            }
        ],
        dom: '<"d-flex justify-content-between mb-2"lfB>rtip',
        buttons: [
            { extend: 'copy', className: 'btn btn-primary btn-sm me-1' },
            { extend: 'csv', className: 'btn btn-primary btn-sm me-1' },
            { extend: 'excel', className: 'btn btn-primary btn-sm me-1' },
            { extend: 'pdf', className: 'btn btn-primary btn-sm me-1' },
            { extend: 'print', className: 'btn btn-primary btn-sm' }
        ],
        pageLength: 10,
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search roles...",
        }
    });
});
</script>
@endsection
