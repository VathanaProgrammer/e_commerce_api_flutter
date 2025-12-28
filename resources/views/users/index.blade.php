@extends('layouts.app')

@section('content')
<x-widget title="Users List">

    <div class="mb-3">
        <a href="{{ route('users.create') }}" class="btn btn-sm btn-success rounded-0">
            <i class="fas fa-plus"></i> Add User
        </a>
    </div>

    <div class="table-responsive w-100 overflow-hidden">
        <table class="table table-sm table-bordered table-striped" id="usersTable">
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

</x-widget>
@endsection

@section('scripts')
<script>
$(document).ready(function() {

    $('#usersTable').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: "{{ route('users.data') }}",
        columns: [
            { data: 'id', name: 'id' },
            { data: 'profile_image_url', name: 'profile_image_url', orderable: false, searchable: false },
            { data: 'full_name', name: 'full_name' },
            { data: 'email', name: 'email' },
            { data: 'username', name: 'username' },
            { data: 'gender', name: 'gender' },
            { data: 'role', name: 'role' },
            { data: 'is_active', name: 'is_active' },
            { data: 'last_login', name: 'last_login' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-center' }
        ],
        order: [[0, 'desc']],
        dom: '<"d-flex justify-content-between mb-2"lfB>rtip',
        buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
        autoWidth: false
    });

});
</script>
@endsection
