@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <x-widget title="Attributes List">

            <div class="mb-4 d-flex justify-content-between align-items-center">
                <button class="btn btn-success rounded-pill px-4 shadow-sm hover-lift" data-bs-toggle="modal"
                    data-bs-target="#addAttributeModal">
                    <i class="bi bi-plus-lg me-2"></i> Add Attribute
                </button>
                <div class="d-flex gap-2">
                    <span class="badge bg-light text-dark rounded-pill px-3 py-2">
                        <i class="bi bi-sliders me-1"></i> Attributes Management
                    </span>
                </div>
            </div>

            <div class="card border-0 shadow-none">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="attributesTable">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Values</th>
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
    @include('attributes.edit')
    @include('attributes.create')

    <style>
        #attributesTable thead th {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            border: none;
            padding: 15px 12px;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            color: #475569;
        }

        #attributesTable tbody tr {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border-bottom: 1px solid #f1f5f9;
        }

        #attributesTable tbody tr:hover {
            background-color: rgba(102, 126, 234, 0.05) !important;
            transform: scale(1.002);
        }

        #attributesTable tbody td {
            padding: 14px 12px;
            vertical-align: middle;
        }

        /* Attribute value badges */
        #attributesTable .badge {
            transition: all 0.2s ease;
        }

        #attributesTable .badge:hover {
            transform: scale(1.05);
        }
    </style>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {

            let table = $('#attributesTable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: "{{ route('attributes.data') }}",
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'values',
                        name: 'values',
                        orderable: false,
                        searchable: false
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
                buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
                autoWidth: false
            });

            // DELETE ATTRIBUTE
            // DELETE ATTRIBUTE
            $(document).on('click', '.delete-attr', function(e) {
                e.preventDefault();

                let url = $(this).data('url');

                showConfirmModal("Are you sure you want to delete this attribute?", function() {
                    $.ajax({
                        url: url,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(res) {
                            if (res.success) {
                                toastr.success(res.message || 'Deleted successfully');
                                table.ajax.reload();
                            } else {
                                toastr.error(res.message || 'Delete failed');
                            }
                        },
                        error: function() {
                            toastr.error('Something went wrong');
                        }
                    });
                });
            });


            $('#addAttributeForm').on('submit', function(e) {
                e.preventDefault();

                let form = $(this);
                let btn = form.find('button[type="submit"]');

                btn.prop('disabled', true).text('Saving...');

                $.ajax({
                    url: "{{ route('attributes.store') }}",
                    method: "POST",
                    data: form.serialize(),
                    success: function(res) {

                        if (res.success) {
                            toastr.success(res.msg || 'Attribute added successfully');

                            // Reset form
                            form[0].reset();

                            // Close modal
                            $('#addAttributeModal').modal('hide');

                            // Reload DataTable
                            $('#attributesTable').DataTable().ajax.reload(null, false);
                        } else {
                            toastr.error(res.msg || 'Failed to add attribute');
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            // Validation error
                            let errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, val) {
                                toastr.error(val[0]);
                            });
                        } else {
                            toastr.error('Something went wrong');
                        }
                    },
                    complete: function() {
                        btn.prop('disabled', false).text('Save');
                    }
                });
            });


            //edit attribute - open modal
            $(document).on('click', '.edit-attr', function() {

                let id = $(this).data('id');
                let name = $(this).data('name');
                let values = $(this).data('values');

                // Fill modal inputs
                $('#edit_attr_id').val(id);
                $('#edit_attr_name').val(name);
                $('#edit_attr_values').val(values);

                // Show modal
                $('#editAttributeModal').modal('show');
            });

            // Update attribute
            $('#editAttributeForm').on('submit', function(e) {
                e.preventDefault();

                let form = $(this);
                let btn = form.find('button[type="submit"]');
                let attrId = $('#edit_attr_id').val();

                btn.prop('disabled', true).text('Updating...');

                $.ajax({
                    url: '/attributes/' + attrId,
                    method: 'PUT',
                    data: form.serialize(),
                    success: function(res) {

                        if (res.success) {
                            toastr.success(res.msg || 'Attribute updated successfully');

                            // Close modal
                            $('#editAttributeModal').modal('hide');

                            // Reload DataTable
                            $('#attributesTable').DataTable().ajax.reload(null, false);
                        } else {
                            toastr.error(res.msg || 'Failed to update attribute');
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            // Validation error
                            let errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, val) {
                                toastr.error(val[0]);
                            });
                        } else {
                            toastr.error('Something went wrong');
                        }
                    },
                    complete: function() {
                        btn.prop('disabled', false).text('Update');
                    }
                });
            });
        });
    </script>
@endsection
