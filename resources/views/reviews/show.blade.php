@extends('layouts.app')

@section('title', 'View Review')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Review Card -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Review Details</h5>
                    <div class="d-flex gap-2">
                        @if(auth()->user()->can('update', $review))
                        <a href="{{ route('reviews.edit', $review) }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        @endif
                        @if(auth()->user()->can('moderate', \App\Models\Review::class))
                        <button class="btn btn-sm btn-warning respond-review" data-id="{{ $review->id }}">
                            <i class="fas fa-reply"></i> Respond
                        </button>
                        <button class="btn btn-sm btn-info feature-review" data-id="{{ $review->id }}">
                            <i class="fas fa-star"></i> {{ $review->is_featured ? 'Unfeature' : 'Feature' }}
                        </button>
                        @endif
                        @if(auth()->user()->can('delete', $review))
                        <button class="btn btn-sm btn-danger delete-review" data-id="{{ $review->id }}">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                        @endif
                        <a href="{{ route('reviews.index') }}" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- User and Product Info -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Reviewer:</strong> {{ $review->user->name }} 
                            @if($review->is_verified_purchase)
                            <span class="badge bg-success">Verified Purchase</span>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <strong>Product:</strong> {{ $review->product->name }}
                        </div>
                    </div>

                    <!-- Overall Rating -->
                    <div class="mb-3">
                        <strong>Overall Rating:</strong>
                        <span class="star-rating ms-2">{{ $review->star_rating }}</span>
                        <span class="ms-2">({{ number_format($review->overall_rating, 1) }}/5)</span>
                    </div>

                    <!-- Review Title and Content -->
                    <div class="mb-3">
                        <h5>{{ $review->title }}</h5>
                        <p class="text-muted">{{ nl2br($review->content) }}</p>
                    </div>

                    <!-- Date and Status -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <small class="text-muted">
                                <i class="fas fa-calendar"></i> {{ $review->created_at->format('M j, Y g:i A') }}
                            </small>
                        </div>
                        <div class="col-md-6">
                            <span class="badge {{ $review->is_approved ? 'bg-success' : 'bg-warning' }}">
                                {{ $review->is_approved ? 'Approved' : 'Pending' }}
                            </span>
                            @if($review->is_featured)
                            <span class="badge bg-info">Featured</span>
                            @endif
                        </div>
                    </div>

                    <!-- Detailed Ratings -->
                    @if($review->ratings->count() > 0)
                    <div class="mb-4">
                        <h6>Detailed Ratings</h6>
                        @foreach($review->ratings as $rating)
                        <div class="row mb-2">
                            <div class="col-md-4">{{ $rating->criterion->name }}:</div>
                            <div class="col-md-8">
                                <div class="d-flex align-items-center">
                                    <span class="star-rating">{{ $rating->star_rating }}</span>
                                    <span class="ms-2">({{ number_format($rating->rating, 1) }}/5)</span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif

                    <!-- Admin Response -->
                    @if($review->admin_response)
                    <div class="alert alert-info">
                        <h6><i class="fas fa-reply"></i> Admin Response</h6>
                        <p class="mb-1">{{ $review->admin_response }}</p>
                        <small class="text-muted">
                            Responded by {{ $review->respondedBy->name }} on {{ $review->admin_response_date->format('M j, Y g:i A') }}
                        </small>
                    </div>
                    @endif

                    <!-- Helpful Votes -->
                    <div class="border-top pt-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <button class="btn btn-sm btn-outline-success vote-helpful" data-id="{{ $review->id }}" data-helpful="1">
                                    <i class="fas fa-thumbs-up"></i> Helpful ({{ $review->helpful_count }})
                                </button>
                                <button class="btn btn-sm btn-outline-secondary vote-helpful ms-2" data-id="{{ $review->id }}" data-helpful="0">
                                    <i class="fas fa-thumbs-down"></i> Not Helpful ({{ $review->total_votes - $review->helpful_count }})
                                </button>
                                <small class="text-muted ms-2">{{ $review->helpful_percentage }}% found this helpful</small>
                            </div>
                            @if(auth()->user()->can('moderate', \App\Models\Review::class))
                            <div>
                                <button class="btn btn-sm btn-success approve-review" data-id="{{ $review->id }}">Approve</button>
                                <button class="btn btn-sm btn-danger reject-review" data-id="{{ $review->id }}">Reject</button>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- All Reviews for This Product -->
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">Other Reviews for {{ $review->product->name }}</h6>
                </div>
                <div class="card-body">
                    <div id="product-reviews">
                        <div class="text-center">
                            <div class="spinner-border" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.star-rating {
    color: #ffc107;
    font-size: 16px;
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Load other reviews for this product
    $.get('{{ route("reviews.product", $review->product) }}', function(response) {
        if (response.success) {
            let reviewsHtml = '';
            
            if (response.data.data.length === 0) {
                reviewsHtml = '<p class="text-muted">No other reviews found for this product.</p>';
            } else {
                response.data.data.forEach(function(review) {
                    if (review.id !== {{ $review->id }}) {
                        reviewsHtml += `
                            <div class="border-bottom pb-3 mb-3">
                                <div class="d-flex justify-content-between">
                                    <strong>${review.user.name}</strong>
                                    <span class="star-rating">${Array(Math.round(review.overall_rating) + 1).join('★')}</span>
                                </div>
                                <h6 class="mt-2">${review.title}</h6>
                                <p class="text-muted small">${review.content.substring(0, 200)}${review.content.length > 200 ? '...' : ''}</p>
                                <small class="text-muted">${new Date(review.created_at).toLocaleDateString()}</small>
                            </div>
                        `;
                    }
                });
            }
            
            $('#product-reviews').html(reviewsHtml);
        }
    });

    // Helpful voting
    $('.vote-helpful').click(function() {
        let reviewId = $(this).data('id');
        let isHelpful = $(this).data('helpful');
        
        $.post('{{ route("reviews.vote-helpful", '') }}/' + reviewId, {
            _token: '{{ csrf_token() }}',
            is_helpful: isHelpful
        }, function(response) {
            if (response.success) {
                toastr.success(response.message);
                location.reload();
            } else {
                toastr.error(response.message);
            }
        });
    });

    // Admin actions
    $('.approve-review').click(function() {
        let id = $(this).data('id');
        if (confirm('Are you sure you want to approve this review?')) {
            $.post('{{ route("reviews.approve") }}/' + id, {
                _token: '{{ csrf_token() }}'
            }, function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    location.reload();
                } else {
                    toastr.error(response.message);
                }
            });
        }
    });

    $('.reject-review').click(function() {
        let id = $(this).data('id');
        if (confirm('Are you sure you want to reject this review?')) {
            $.post('{{ route("reviews.reject") }}/' + id, {
                _token: '{{ csrf_token() }}'
            }, function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    location.reload();
                } else {
                    toastr.error(response.message);
                }
            });
        }
    });

    $('.feature-review').click(function() {
        let id = $(this).data('id');
        $.post('{{ route("reviews.feature") }}/' + id, {
            _token: '{{ csrf_token() }}'
        }, function(response) {
            if (response.success) {
                toastr.success(response.message);
                location.reload();
            } else {
                toastr.error(response.message);
            }
        });
    });

    $('.respond-review').click(function() {
        let id = $(this).data('id');
        window.location.href = `#response-${id}`;
    });

    $('.delete-review').click(function() {
        let id = $(this).data('id');
        if (confirm('Are you sure you want to delete this review? This action cannot be undone.')) {
            $.ajax({
                url: '{{ route("reviews.destroy") }}/' + id,
                type: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        window.location.href = '{{ route("reviews.index") }}';
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