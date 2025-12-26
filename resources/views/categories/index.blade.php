@extends('layouts.app')

@section('content')
    @include('categories.edit')
    @include('categories.create')
    <x-widget title="Category List">

        <div class="mb-3">
            <button class="btn btn-sm btn-success rounded-0" data-bs-toggle="modal" data-bs-target="#createCategoryModal">
                <i class="fas fa-plus"></i> Create New Category
            </button>
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
                dom: '<"d-flex justify-content-between mb-2"lfB>rtip', // Better spacing
                buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
                scrollX: false,
                autoWidth: false,
                initComplete: function() {
                    $('#categoriesTable_wrapper .dataTables_filter').css('margin-bottom', '10px');
                    $('#categoriesTable_wrapper .dataTables_length').css('margin-bottom', '10px');
                }
            });

            // Handle delete button
            $(document).on('click', '.delete-category', function(e) {
                e.preventDefault();
                let url = $(this).data('url');

                showConfirmModal("Are you sure you want to delete this category?", function() {
                    $.ajax({
                        url: url,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.data.success) {
                                $('#categoriesTable').DataTable().ajax.reload();
                                toastr.success(response.data.mesg ||
                                    'Deleted successfully.');
                            } else {
                                toastr.success(response.data.mesg ||
                                    'Deleted failed.');
                            }
                        }
                    });
                });
            });

            $('#createCategoryModal form').on('submit', function(e) {
                e.preventDefault();
                let form = $(this);
                let categoryName = form.find('input[name="name"]').val().trim();

                if (!categoryName) {
                    // Hide modal first
                    $('#createCategoryModal').modal('hide');

                    // Show toastr after modal fully closed
                    $('#createCategoryModal').on('hidden.bs.modal.toastError', function() {
                        toastr.error('Please enter category name!');
                        $('#createCategoryModal').off(
                            'hidden.bs.modal.toastError'); // remove listener
                    });

                    return;
                }

                $.ajax({
                    url: "{{ route('categories.store') }}",
                    method: "POST",
                    data: form.serialize(),
                    success: function(res) {
                        $('select[name="category_id"]').append(
                            `<option value="${res.data.id}" selected>${res.data.name}</option>`
                        );
                        console.log('Category added successfully', res.data);

                        if (res.data.success) {
                            toastr.success(res.data.msg || 'Category added successfully');
                            form[0].reset();
                            $('#createCategoryModal').modal('hide');

                        } else {
                            toastr.error(res.data.msg || 'Failed to add category');
                        }

                    },
                    error: function(err) {
                        $('#createCategoryModal').modal('hide');

                        $('#createCategoryModal').on('hidden.bs.modal.toastFail', function() {
                            toastr.error('Failed to add category');
                            $('#createCategoryModal').off('hidden.bs.modal.toastFail');
                        });
                    }
                });
            });

            $(document).on('click', '.edit-category', function(e) {
                e.preventDefault();

                $('#editCategoryModal').modal('show');


                

                let categoryId = $(this).data('id');
                let categoryName = $(this).data('name');
                let url = '/categories/update/' + categoryId;

                // Set modal inputs
                $('#editCategoryModal form input[name="name"]').val(categoryName);
                $('#editCategoryModal form input[name="cate_id"]').val(categoryId);

                // Handle form submit inside the modal
                $('#editCategoryModal form').off('submit').on('submit', function(e) {
                    e.preventDefault();

                    $.ajax({
                        url: url,
                        type: 'PUT',
                        data: {
                            _token: '{{ csrf_token() }}',
                            name: $('#editCategoryModal form input[name="name"]').val()
                        },
                        success: function(response) {
                            if (response.data.success) {
                                toastr.success(response.data.msg ||
                                    'Category updated successfully');
                                $('#editCategoryModal').modal('hide');
                                $('#categoriesTable').DataTable().ajax.reload();
                            } else {
                                toastr.error(response.data.msg ||
                                    'Category update failed.');
                            }
                        },
                        error: function(xhr) {
                            toastr.error('Something went wrong. Please try again.');
                        }
                    });
                });
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
            background-color: white !important;
            /* ensures readability */
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
