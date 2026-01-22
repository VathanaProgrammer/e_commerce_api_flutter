@extends('layouts.app')

@section('content')
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <x-widget title="Products">
                    <div class="mb-3">
                        <a href="{{ route('products.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Add New Product
                        </a>
                    </div>
                    <table id="productsTable" class="table table-hover table-striped">
                        <thead class="">
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
                </x-widget>
            </div>
        </div>
    </div>

    <style>
        #productsTable {
            font-size: 0.9rem;
        }
        
        .product-image {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 4px;
        }
        
        .product-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .variant-badge {
            display: inline-block;
            padding: 4px 8px;
            margin: 2px;
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            font-size: 0.8rem;
        }
        
        .variant-item {
            padding: 6px 10px;
            margin: 3px 0;
            background: #f8f9fa;
            border-left: 3px solid #007bff;
            border-radius: 3px;
            font-size: 0.85rem;
        }
        
        .variant-sku {
            font-weight: 600;
            color: #495057;
        }
        
        .variant-price {
            color: #28a745;
            font-weight: 600;
        }
        
        .variant-attrs {
            font-size: 0.75rem;
            color: #6c757d;
            margin-top: 2px;
        }
        
        .desc-line {
            padding: 3px 0;
            font-size: 0.85rem;
            line-height: 1.4;
        }
        
        .desc-line:before {
            content: "• ";
            color: #6c757d;
        }
        
        .status-badge {
            font-size: 0.75rem;
            padding: 4px 8px;
            border-radius: 12px;
        }
        
        .variants-scrollable {
            max-height: 200px;
            overflow-y: auto;
            padding-right: 5px;
        }
        
        .variants-scrollable::-webkit-scrollbar {
            width: 6px;
        }
        
        .variants-scrollable::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        
        .variants-scrollable::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 10px;
        }
        
        .variants-scrollable::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
        
        .variant-count-badge {
            background: #007bff;
            color: white;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 0.75rem;
            font-weight: 600;
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
                dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"lfB>>rtip',
                buttons: ['copy', 'csv', 'excel', 'print', 'pdf', 'colvis'],
                pageLength: 25,
                lengthMenu: [
                    [10, 25, 50, 100],
                    [10, 25, 50, 100]
                ],
                order: [[0, 'desc']],
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search products..."
                }
            });

            $(document).on('click', '.delete-product', function(e) {
                e.preventDefault();
                if (!confirm('Are you sure you want to delete this product?')) return;
                
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
        });
    </script>
@endsection