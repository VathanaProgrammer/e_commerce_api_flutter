@extends('layouts.app')

@section('title', 'Reviews Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Reviews Management</h4>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#filterModal">
                            <i class="fas fa-filter"></i> Filter
                        </button>
                        @if(auth()->user()->can('create', \App\Models\Review::class))
                        <a href="{{ route('reviews.create') }}" class="btn btn-success">
                            <i class="fas fa-plus"></i> Add Review
                        </a>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="reviews-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>User</th>
                                    <th>Product</th>
                                    <th>Title</th>
                                    <th>Rating</th>
                                    <th>Status</th>
                                    <th>Verified</th>
                                    <th>Helpful</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filter Modal -->
<div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="filterModalLabel">Filter Reviews</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="filter-form">
                    <div class="mb-3">
                        <label for="filter-product" class="form-label">Product</label>
                        <select class="form-select" id="filter-product" name="product_id">
                            <option value="">All Products</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="filter-status" class="form-label">Status</label>
                        <select class="form-select" id="filter-status" name="status">
                            <option value="">All Status</option>
                            <option value="approved">Approved</option>
                            <option value="pending">Pending</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="filter-verified" class="form-label">Verified Purchase</label>
                        <select class="form-select" id="filter-verified" name="verified">
                            <option value="">All</option>
                            <option value="1">Verified Only</option>
                            <option value="0">Unverified Only</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="filter-rating" class="form-label">Minimum Rating</label>
                        <select class="form-select" id="filter-rating" name="rating">
                            <option value="">All Ratings</option>
                            <option value="1">1 Star & Up</option>
                            <option value="2">2 Stars & Up</option>
                            <option value="3">3 Stars & Up</option>
                            <option value="4">4 Stars & Up</option>
                            <option value="5">5 Stars Only</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="apply-filter">Apply Filter</button>
                <button type="button" class="btn btn-outline-secondary" id="clear-filter">Clear</button>
            </div>
        </div>
    </div>
</div>

<!-- Admin Response Modal -->
<div class="modal fade" id="responseModal" tabindex="-1" aria-labelledby="responseModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="responseModalLabel">Admin Response</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="response-form">
                    <input type="hidden" id="response-review-id">
                    <div class="mb-3">
                        <label for="admin-response" class="form-label">Response</label>
                        <textarea class="form-control" id="admin-response" rows="4" required maxlength="1000"></textarea>
                        <div class="form-text">Maximum 1000 characters</div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="save-response">Save Response</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .star-rating {
        color: #ffc107;
    }
    .status-approved {
        color: #28a745;
        font-weight: bold;
    }
    .status-pending {
        color: #ffc107;
        font-weight: bold;
    }
    .verified-badge {
        color: #17a2b8;
    }
    .helpful-count {
        color: #6c757d;
        font-size: 0.9em;
    }
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    let dataTable = $('#reviews-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("reviews.datatable") }}',
            type: 'GET',
            data: function(d) {
                d.product_id = $('#filter-product').val();
                d.status = $('#filter-status').val();
                d.verified = $('#filter-verified').val();
                d.rating = $('#filter-rating').val();
            }
        },
        columns: [
            { data: 'id', name: 'id' },
            { data: 'user_name', name: 'user.name' },
            { data: 'product_name', name: 'product.name' },
            { data: 'title', name: 'title' },
            { data: 'overall_rating', name: 'overall_rating' },
            { data: 'status', name: 'status' },
            { data: 'is_verified_purchase', name: 'is_verified_purchase' },
            { data: 'helpful_count', name: 'helpful_count' },
            { data: 'created_at', name: 'created_at' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ],
        order: [[0, 'desc']],
        pageLength: 25
    });

    // Load products for filter
    $.get('{{ route("products.list") }}', function(data) {
        let select = $('#filter-product');
        data.forEach(function(product) {
            select.append('<option value="' + product.id + '">' + product.name + '</option>');
        });
    });

    // Filter actions
    $('#apply-filter').click(function() {
        dataTable.ajax.reload();
        $('#filterModal').modal('hide');
    });

    $('#clear-filter').click(function() {
        $('#filter-form')[0].reset();
        dataTable.ajax.reload();
    });

    // Review actions
    $(document).on('click', '.approve-review', function() {
        let id = $(this).data('id');
        if (confirm('Are you sure you want to approve this review?')) {
            $.post('{{ route("reviews.approve", "REPLACE_ID") }}'.replace('REPLACE_ID', id), {
                _token: '{{ csrf_token() }}'
            }, function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    dataTable.ajax.reload();
                } else {
                    toastr.error(response.message);
                }
            });
        }
    });

    $(document).on('click', '.reject-review', function() {
        let id = $(this).data('id');
        if (confirm('Are you sure you want to reject this review?')) {
            $.post('{{ route("reviews.reject", "REPLACE_ID") }}'.replace('REPLACE_ID', id), {
                _token: '{{ csrf_token() }}'
            }, function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    dataTable.ajax.reload();
                } else {
                    toastr.error(response.message);
                }
            });
        }
    });

    $(document).on('click', '.feature-review', function() {
        let id = $(this).data('id');
        $.post('{{ route("reviews.feature", "REPLACE_ID") }}'.replace('REPLACE_ID', id), {
            _token: '{{ csrf_token() }}'
        }, function(response) {
            if (response.success) {
                toastr.success(response.message);
                dataTable.ajax.reload();
            } else {
                toastr.error(response.message);
            }
        });
    });

    $(document).on('click', '.respond-review', function() {
        let id = $(this).data('id');
        $('#response-review-id').val(id);
        $('#responseModal').modal('show');
    });

    $('#save-response').click(function() {
        let id = $('#response-review-id').val();
        let response = $('#admin-response').val();
        
        $.post('{{ route("reviews.respond", "REPLACE_ID") }}'.replace('REPLACE_ID', id), {
            _token: '{{ csrf_token() }}',
            response: response
        }, function(result) {
            if (result.success) {
                toastr.success(result.message);
                $('#responseModal').modal('hide');
                $('#admin-response').val('');
                dataTable.ajax.reload();
            } else {
                toastr.error(result.message);
            }
        });
    });

    $(document).on('click', '.delete-review', function() {
        let id = $(this).data('id');
        if (confirm('Are you sure you want to delete this review?')) {
            $.ajax({
                url: '{{ route("reviews.destroy", "REPLACE_ID") }}'.replace('REPLACE_ID', id),
                type: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        dataTable.ajax.reload();
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function() {
                    toastr.error('Error deleting review');
                }
            });
        }
    });
});
</script>
@endpush