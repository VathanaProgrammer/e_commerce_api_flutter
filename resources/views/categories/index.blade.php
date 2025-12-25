@extends('layouts.app')

@section('content')
<x-widget title="Category List">

    <div class="mb-3">
        <a href="{{ route('categories.create') }}" class="btn btn-sm btn-success rounded-0">
            <i class="bi bi-plus-square me-1"></i> Add Category
        </a>
    </div>

    <div class="table-responsive w-100 overflow-hidden">
        <table class="table table-sm table-bordered table-striped" id="categoriesTable">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Created At</th>
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
    $('#categoriesTable').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: "{{ route('categories.data') }}",
        columns: [
            { data: 'id', name: 'id' },
            { data: 'name', name: 'name' },
            { data: 'created_at', name: 'created_at' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-center' }
        ],
        order: [[0, 'desc']],
        dom: '<"d-flex justify-content-between mb-2"lfB>rtip', // Better spacing
        buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
        scrollX: false,
        autoWidth: false,
        initComplete: function(){
            $('#categoriesTable_wrapper .dataTables_filter').css('margin-bottom','10px');
            $('#categoriesTable_wrapper .dataTables_length').css('margin-bottom','10px');
        }
    });

    // Handle delete button
    $(document).on('click', '.delete-category', function(e) {
        e.preventDefault();
        let url = $(this).data('url');
        if (confirm('Are you sure you want to delete this category?')) {
            $.ajax({
                url: url,
                type: 'DELETE',
                data: {_token: '{{ csrf_token() }}'},
                success: function(response) {
                    $('#categoriesTable').DataTable().ajax.reload();
                    alert(response.message || 'Category deleted successfully.');
                }
            });
        }
    });
});
</script>

<style>
/* Force all text in DataTable and Buttons black */
#categoriesTable,
#categoriesTable th,
#categoriesTable td,
#categoriesTable_wrapper .dataTables_info,
#categoriesTable_wrapper .dataTables_length,
#categoriesTable_wrapper .dataTables_filter,
#categoriesTable_wrapper .dataTables_paginate,
#categoriesTable_wrapper .dt-buttons button,
#categoriesTable_wrapper .dataTables_filter input {
    color: black !important;
    background-color: white !important; /* ensures readability */
}

/* Optional: bold headers */
#categoriesTable thead th {
    font-weight: bold;
}

/* Buttons styling */
#categoriesTable_wrapper .dt-buttons button {
    border: 1px solid #ccc;
    background-color: #fff;
    color: black;
    padding: 4px 8px;
    margin-right: 4px;
}

/* Spacing */
#categoriesTable_wrapper {
    margin-top: 20px;
}
#categoriesTable_wrapper .dataTables_filter {
    margin-bottom: 10px;
}
#categoriesTable_wrapper .dataTables_length {
    margin-bottom: 10px;
}
</style>
@endsection
