@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <x-widget title="Users List">

            <div class="mb-3">
                <a href="{{ route('users.create') }}" class="btn btn-success btn-sm rounded-0">
                    <i class="fas fa-plus"></i> Add User
                </a>
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
