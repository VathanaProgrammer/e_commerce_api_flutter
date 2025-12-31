@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <div class="row">
            <div class="col-12">
                <x-widget title="Products">
                    <div class="card border-0 shadow-none">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table id="productsTable" class="table display nowrap">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Name</th>
                                            <th>Category</th>
                                            <th>Variants</th>
                                            <th>Description</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </x-widget>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $('#productsTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('products.data') }}',
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'category',
                        name: 'category.name'
                    },
                    {
                        data: 'variants',
                        name: 'variants',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'description',
                        name: 'description',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],
                dom: '<"d-flex justify-content-between mb-2"lfB>rtip',
                buttons: ['copy', 'csv', 'excel', 'print'],
                pageLength: 10,
                lengthMenu: [
                    [10, 25, 50, -1],
                    [10, 25, 50, "All"]
                ],
                initComplete: function() {
                    $('#productsTable_wrapper .dataTables_filter').css('margin-bottom', '10px');
                    $('#productsTable_wrapper .dataTables_length').css('margin-bottom', '10px');
                }
            });

            $(document).on('click', '.delete-product', function(e) {
                e.preventDefault();
                if (!confirm('Are you sure to delete this product?')) return;
                let url = $(this).attr('href');
                $.ajax({
                    url: url,
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function() {
                        $('#productsTable').DataTable().ajax.reload();
                        toastr.success('Product deleted successfully');
                    },
                    error: function() {
                        toastr.error('Failed to delete product');
                    }
                });
            });

            // Open Edit Product modal
            $(document).on('click', '.edit-product', function() {
                let productId = $(this).data('id');

                $.ajax({
                    url: '/products/' + productId + '/edit', // make sure you have a show route
                    method: 'GET',
                    success: function(res) {
                        let product = res.data;

                        $('#editProductModal input[name="product_id"]').val(product.id);
                        $('#editProductModal input[name="name"]').val(product.name);
                        $('#editProductModal select[name="category_id"]').val(product
                            .category_id);

                        // Render description lines
                        let descHtml = '';
                        product.description_lines.forEach(line => {
                            descHtml += `
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-auto">
                        <input type="text" name="description_lines[]" class="form-control form-control-sm rounded-0" value="${line.text}">
                    </div>
                    <div class="col-auto">
                        <button type="button" class="btn btn-sm btn-outline-danger removeLine">Remove</button>
                    </div>
                </div>`;
                        });
                        $('#editDescriptionLines').html(descHtml);

                        // Render attributes
                        let attrHtml = '';
                        product.attributes.forEach(attr => {
                            attrHtml += `
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-auto">
                        <input type="text" name="attributes[]" class="form-control form-control-sm rounded-0" value="${attr.name}">
                    </div>
                    <div class="col-auto">
                        <button type="button" class="btn btn-sm btn-outline-danger removeAttr">Remove</button>
                    </div>
                </div>`;
                        });
                        $('#editAttributesSection').html(attrHtml);

                        // Render variants
                        let variantHtml = '';
                        product.variants.forEach(v => {
                            variantHtml += `
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-auto">
                        <input type="text" name="variant_sku[]" class="form-control form-control-sm rounded-0" value="${v.sku}">
                    </div>
                    <div class="col-auto">
                        <input type="number" name="variant_price[]" class="form-control form-control-sm rounded-0" value="${v.price}">
                    </div>
                    <div class="col-auto">
                        <button type="button" class="btn btn-sm btn-outline-danger removeVariant">Remove</button>
                    </div>
                </div>`;
                        });
                        $('#editVariantsSection').html(variantHtml);

                        $('#editProductModal').modal('show');
                    }
                });
            });

        });
    </script>

    <style>
        /* Make table and all related text black */
        #productsTable,
        #productsTable th,
        #productsTable td,
        #productsTable_wrapper .dataTables_info,
        #productsTable_wrapper .dataTables_length,
        #productsTable_wrapper .dataTables_filter,
        #productsTable_wrapper .dataTables_paginate,
        #productsTable_wrapper .dt-buttons button,
        #productsTable_wrapper .dataTables_filter input {
            color: black !important;
            background-color: white !important;
            /* ensures readable background */
        }

        /* Bold header */
        #productsTable thead th {
            font-weight: bold;
        }

        /* Buttons styling */
        #productsTable_wrapper .dt-buttons button {
            border: 1px solid #ccc;
            background-color: #fff;
            color: black;
            padding: 4px 8px;
            margin-right: 4px;
        }

        /* Optional spacing */
        #productsTable_wrapper {
            margin-top: 20px;
        }

        #productsTable_wrapper .dataTables_filter {
            margin-bottom: 10px;
        }

        #productsTable_wrapper .dataTables_length {
            margin-bottom: 10px;
        }
    </style>
@endsection
