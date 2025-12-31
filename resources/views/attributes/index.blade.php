@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <x-widget title="Attributes List">

            <div class="mb-3">
                <button class="btn btn-success btn-sm rounded-0" data-bs-toggle="modal"
                    data-bs-target="#addAttributeModal">
                    <i class="fas fa-plus"></i> Add Attribute
                </button>
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
            $(document).on('click', '.delete-attr', function(e) {
                e.preventDefault();

                let url = $(this).data('url');

                if (!confirm('Delete this attribute?')) return;

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
