@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <x-widget title="Users List">

            <div class="mb-4 d-flex justify-content-between align-items-center">
                <a href="{{ route('users.create') }}" class="btn btn-success rounded-pill px-4 shadow-sm hover-lift">
                    <i class="bi bi-person-plus me-2"></i> Add User
                </a>
                <div class="d-flex gap-2">
                    <span class="badge bg-light text-dark rounded-pill px-3 py-2">
                        <i class="bi bi-people me-1"></i> User Management
                    </span>
                </div>
            </div>

            <div class="card border-0 shadow-none">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="usersTable">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Profile</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Username</th>
                                    <th>Gender</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th>Last Login</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>

        </x-widget>
    </div>

    <style>
        #usersTable thead th {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            border: none;
            padding: 15px 12px;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            color: #475569;
        }

        #usersTable tbody tr {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border-bottom: 1px solid #f1f5f9;
        }

        #usersTable tbody tr:hover {
            background-color: rgba(102, 126, 234, 0.05) !important;
            transform: scale(1.002);
        }

        #usersTable tbody td {
            padding: 12px;
            vertical-align: middle;
        }

        #usersTable img {
            transition: all 0.3s ease;
            border-radius: 50%;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        #usersTable img:hover {
            transform: scale(1.15);
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }
    </style>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {

            $('#usersTable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: "{{ route('users.data') }}",
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'profile_image_url',
                        name: 'profile_image_url',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'full_name',
                        name: 'full_name'
                    },
                    {
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'username',
                        name: 'username'
                    },
                    {
                        data: 'gender',
                        name: 'gender'
                    },
                    {
                        data: 'role',
                        name: 'role'
                    },
                    {
                        data: 'is_active',
                        name: 'is_active'
                    },
                    {
                        data: 'last_login',
                        name: 'last_login'
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    }
                ],
                order: [
                    [0, 'desc']
                ],
                dom: '<"d-flex justify-content-between mb-2"lfB>rtip',
                buttons: [{
                        extend: 'copy',
                        className: 'btn btn-primary btn-sm me-1'
                    },
                    {
                        extend: 'csv',
                        className: 'btn btn-primary btn-sm me-1'
                    },
                    {
                        extend: 'excel',
                        className: 'btn btn-primary btn-sm me-1'
                    },
                    {
                        extend: 'pdf',
                        className: 'btn btn-primary btn-sm me-1'
                    },
                    {
                        extend: 'print',
                        className: 'btn btn-primary btn-sm'
                    }
                ],
                autoWidth: false
            });

            // Delete User
            $(document).on('click', '.delete-user', function(e) {
                e.preventDefault();
                let url = $(this).data('url');

                showConfirmModal("Are you sure you want to delete this user?", function() {
                    $.ajax({
                        url: url,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(res) {
                            if (res.success) {
                                toastr.success('User deleted successfully');
                                $('#usersTable').DataTable().ajax.reload();
                            } else {
                                toastr.error('Failed to delete user');
                            }
                        },
                        error: function() {
                            toastr.error('Something went wrong');
                        }
                    });
                });
            });

        });
    </script>
@endsection

{{-- @section('styles')
    <style>
        /* Remove all table borders */
        #usersTable,
        #usersTable th,
        #usersTable td {
            border: none !important;
        }

        /* Optional: remove inner padding if needed */
        #usersTable th,
        #usersTable td {
            padding: 0.5rem 0.75rem;
        }
    </style>
@endsection --}}
