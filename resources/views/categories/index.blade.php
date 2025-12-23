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
                responsive: true, // makes table responsive
                ajax: "{{ route('categories.data') }}",
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
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
                dom: 'Bfrtip',
                buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
                scrollX: false, // make sure horizontal scroll is off
                autoWidth: false // prevent DataTables from forcing column width
            });


            // Handle delete button
            $(document).on('click', '.delete-category', function(e) {
                e.preventDefault();
                let url = $(this).data('url');
                if (confirm('Are you sure you want to delete this category?')) {
                    $.ajax({
                        url: url,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            $('#categoriesTable').DataTable().ajax.reload();
                            alert(response.message || 'Category deleted successfully.');
                        }
                    });
                }
            });
        });
    </script>
@endsection
