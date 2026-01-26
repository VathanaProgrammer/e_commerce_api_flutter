@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <div class="row">
            <div class="col-12">
                <x-widget title="Products">
                    <div class="mb-4 d-flex justify-content-between align-items-center">
                        <a href="{{ route('products.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm hover-lift">
                            <i class="bi bi-plus-lg me-2"></i> Add New Product
                        </a>
                        <div class="d-flex gap-2">
                            <span class="badge bg-light text-dark rounded-pill px-3 py-2">
                                <i class="bi bi-box-seam me-1"></i> Products Management
                            </span>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table id="productsTable" class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%">ID</th>
                                    <th width="15%">Product</th>
                                    <th width="12%">Category</th>
                                    <th width="10%">Status</th>
                                    <th width="30%">Variants</th>
                                    <th width="18%">Description</th>
                                    <th width="10%">Actions</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </x-widget>
            </div>
        </div>
    </div>

    <style>
        #productsTable {
            font-size: 0.9rem;
        }

        #productsTable thead th {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            border: none;
            padding: 15px 12px;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            color: #475569;
        }

        #productsTable tbody tr {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border-bottom: 1px solid #f1f5f9;
        }

        #productsTable tbody tr:hover {
            background-color: rgba(102, 126, 234, 0.05) !important;
            transform: scale(1.002);
        }

        #productsTable tbody td {
            padding: 15px 12px;
            vertical-align: middle;
        }

        .product-image {
            width: 55px;
            height: 55px;
            object-fit: cover;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }

        .product-image:hover {
            transform: scale(1.1);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        .product-info {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .variant-badge {
            display: inline-block;
            padding: 5px 10px;
            margin: 2px;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-size: 0.8rem;
            transition: all 0.2s ease;
        }

        .variant-badge:hover {
            background: linear-gradient(135deg, #e2e8f0 0%, #cbd5e1 100%);
            transform: translateY(-1px);
        }

        .variant-item {
            padding: 8px 12px;
            margin: 4px 0;
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border-left: 3px solid #667eea;
            border-radius: 6px;
            font-size: 0.85rem;
            transition: all 0.2s ease;
        }

        .variant-item:hover {
            transform: translateX(3px);
            border-left-color: #764ba2;
        }

        .variant-sku {
            font-weight: 600;
            color: #1e293b;
        }

        .variant-price {
            color: #10b981;
            font-weight: 700;
        }

        .variant-attrs {
            font-size: 0.75rem;
            color: #64748b;
            margin-top: 3px;
        }

        .desc-line {
            padding: 4px 0;
            font-size: 0.85rem;
            line-height: 1.5;
            color: #475569;
        }

        .desc-line:before {
            content: "• ";
            color: #667eea;
            font-weight: bold;
        }

        .status-badge {
            font-size: 0.75rem;
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: 600;
        }

        .variants-scrollable {
            max-height: 200px;
            overflow-y: auto;
            padding-right: 8px;
        }

        .variants-scrollable::-webkit-scrollbar {
            width: 5px;
        }

        .variants-scrollable::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 10px;
        }

        .variants-scrollable::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 10px;
        }

        .variants-scrollable::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(135deg, #5a67d8 0%, #6b46c1 100%);
        }

        .variant-count-badge {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            box-shadow: 0 2px 6px rgba(102, 126, 234, 0.3);
        }

        /* Action buttons */
        .btn-action {
            width: 32px;
            height: 32px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            transition: all 0.2s ease;
        }

        .btn-action:hover {
            transform: scale(1.1);
        }
    </style>
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
                        name: 'name',
                        render: function(data, type, row) {
                            let imageUrl = row.image_url ? row.image_url : '/placeholder.png';
                            return `
                                <div class="product-info">
                                    <img src="${imageUrl}" class="product-image" alt="${data}">
                                    <strong>${data}</strong>
                                </div>
                            `;
                        }
                    },
                    {
                        data: 'category',
                        name: 'category.name'
                    },
                    {
                        data: 'status',
                        name: 'status',
                        orderable: false,
                        searchable: false
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
                dom: '<"row mb-2"' +
                    '<"col-md-4"l>' +
                    '<"col-md-4 text-center"B>' +
                    '<"col-md-4 text-end"f>' +
                    '>' +
                    'rtip',
                buttons: ['copy', 'csv', 'excel', 'print', 'pdf', 'colvis'],
                pageLength: 25,
                lengthMenu: [
                    [10, 25, 50, 100],
                    [10, 25, 50, 100]
                ],
                order: [
                    [0, 'desc']
                ],
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search products..."
                }
            });

            $(document).on('click', '.delete-product', function(e) {
                e.preventDefault();
                let url = $(this).attr('href');

                showConfirmModal("Are you sure you want to delete this product?", function() {
                    $.ajax({
                        url: url,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(res) {
                            $('#productsTable').DataTable().ajax.reload();
                            toastr.success(res.message || 'Product deleted successfully');
                        },
                        error: function() {
                            toastr.error('Failed to delete product');
                        }
                    });
                });
            });
        });
    </script>
@endsection
