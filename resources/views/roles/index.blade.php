@extends('layouts.app')

@section('title', 'Roles & Permissions')

@push('styles')
<style>
    :root {
        --premium-primary: #667eea;
        --premium-secondary: #764ba2;
        --premium-bg: #f8fafc;
        --premium-card-bg: #ffffff;
        --premium-border: #e2e8f0;
        --premium-text: #1e293b;
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
        transition: all 0.2s;
    }

    .permission-badge:hover {
        background: var(--premium-primary);
        color: white;
        border-color: var(--premium-primary);
        transform: translateY(-1px);
    }

    .role-name-cell {
        font-weight: 700;
        color: var(--premium-text);
        font-size: 1rem;
    }

    .btn-create-role {
        background: linear-gradient(135deg, var(--premium-primary) 0%, var(--premium-secondary) 100%);
        color: white;
        border: none;
        border-radius: 12px;
        padding: 10px 20px;
        font-weight: 600;
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        transition: all 0.3s;
    }

    .btn-create-role:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(102, 126, 234, 0.4);
        color: white;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: var(--premium-primary) !important;
        border-color: var(--premium-primary) !important;
        color: white !important;
        border-radius: 8px !important;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <x-widget title="Roles & Permissions">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <p class="text-muted small mb-0">System access levels and granular authority management</p>
            </div>
            <a href="{{ route('roles.create') }}" class="btn-create-role">
                <i class="bi bi-shield-plus me-2"></i>Create New Role
            </a>
        </div>

        <div class="table-responsive">
            <table class="table align-middle" id="rolesTable" style="width:100%">
                <thead>
                    <tr class="text-secondary small text-uppercase fw-bold">
                        <th class="border-0 ps-4">Role Name</th>
                        <th class="border-0">Assigned Permissions</th>
                        <th class="border-0 text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody class="border-0">
                    <!-- DataTables content -->
                </tbody>
            </table>
        </div>
    </x-widget>
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
            { 
                data: 'name', 
                name: 'name', 
                className: 'role-name-cell ps-4',
                render: function(data) {
                    return `<div class="d-flex align-items-center">
                                <div class="rounded-circle bg-light d-flex justify-content-center align-items-center me-3" style="width:35px; height:35px">
                                    <i class="bi bi-shield-fill-check text-primary"></i>
                                </div>
                                <span>${data}</span>
                            </div>`;
                }
            },
            { 
                data: 'permissions', 
                name: 'permissions', 
                orderable: false, 
                searchable: false,
                render: function(data) {
                    // Expecting HTML from controller, but we can refine it if needed
                    return `<div class="d-flex flex-wrap" style="max-width: 600px">${data}</div>`;
                }
            },
            { 
                data: 'actions', 
                name: 'actions', 
                orderable: false, 
                searchable: false, 
                className: 'text-end pe-4' 
            }
        ],
        dom: '<"d-flex justify-content-between align-items-center mb-3"lf>rt<"d-flex justify-content-between align-items-center mt-3"ip>',
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search roles...",
            lengthMenu: "Show _MENU_",
        },
        pageLength: 10,
        drawCallback: function() {
            // Already handled by controller
        }
    });

    // Style the search input
    $('.dataTables_filter input').addClass('form-control form-control-sm border-2 rounded-pill ps-3').css('width', '250px');
    $('.dataTables_length select').addClass('form-select form-select-sm border-2 rounded-pill mx-2');
});
</script>
@endsection
