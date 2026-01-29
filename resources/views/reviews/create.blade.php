@extends('layouts.app')

@section('title', 'Create Review')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Create Review</h4>
                </div>
                <div class="card-body">
                    <form id="review-form">
                        @csrf
                        <div class="mb-3">
                            <label for="product_id" class="form-label">Product *</label>
                            <select class="form-select" id="product_id" name="product_id" required>
                                <option value="">Select a Product</option>
                                @foreach($products as $product)
                                <option value="{{ $product->id }}">{{ $product->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Overall Rating *</label>
                            <div class="star-rating-container">
                                <input type="radio" id="star5" name="overall_rating" value="5" required>
                                <label for="star5" class="star">★</label>
                                <input type="radio" id="star4" name="overall_rating" value="4">
                                <label for="star4" class="star">★</label>
                                <input type="radio" id="star3" name="overall_rating" value="3">
                                <label for="star3" class="star">★</label>
                                <input type="radio" id="star2" name="overall_rating" value="2">
                                <label for="star2" class="star">★</label>
                                <input type="radio" id="star1" name="overall_rating" value="1">
                                <label for="star1" class="star">★</label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="title" class="form-label">Review Title *</label>
                            <input type="text" class="form-control" id="title" name="title" required maxlength="255">
                            <div class="form-text">Maximum 255 characters</div>
                        </div>

                        <div class="mb-3">
                            <label for="content" class="form-label">Review Content *</label>
                            <textarea class="form-control" id="content" name="content" rows="6" required maxlength="2000"></textarea>
                            <div class="form-text">Minimum 10 characters, maximum 2000 characters</div>
                        </div>

                        @if($criteria->count() > 0)
                        <div class="mb-4">
                            <h5>Detailed Ratings</h5>
                            @foreach($criteria as $criterion)
                            <div class="mb-3">
                                <label class="form-label">{{ $criterion->name }}</label>
                                @if($criterion->description)
                                <small class="text-muted d-block">{{ $criterion->description }}</small>
                                @endif
                                <div class="star-rating-container">
                                    <input type="radio" id="criterion_{{ $criterion->id }}_5" name="ratings[{{ $criterion->id }}]" value="5">
                                    <label for="criterion_{{ $criterion->id }}_5" class="star">★</label>
                                    <input type="radio" id="criterion_{{ $criterion->id }}_4" name="ratings[{{ $criterion->id }}]" value="4">
                                    <label for="criterion_{{ $criterion->id }}_4" class="star">★</label>
                                    <input type="radio" id="criterion_{{ $criterion->id }}_3" name="ratings[{{ $criterion->id }}]" value="3">
                                    <label for="criterion_{{ $criterion->id }}_3" class="star">★</label>
                                    <input type="radio" id="criterion_{{ $criterion->id }}_2" name="ratings[{{ $criterion->id }}]" value="2">
                                    <label for="criterion_{{ $criterion->id }}_2" class="star">★</label>
                                    <input type="radio" id="criterion_{{ $criterion->id }}_1" name="ratings[{{ $criterion->id }}]" value="1">
                                    <label for="criterion_{{ $criterion->id }}_1" class="star">★</label>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @endif

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('reviews.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">Submit Review</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.star-rating-container {
    display: flex;
    flex-direction: row-reverse;
    gap: 5px;
}

.star-rating-container input[type="radio"] {
    display: none;
}

.star-rating-container .star {
    font-size: 24px;
    color: #ddd;
    cursor: pointer;
    transition: color 0.2s;
}

.star-rating-container .star:hover,
.star-rating-container .star:hover ~ .star {
    color: #ffc107;
}

.star-rating-container input[type="radio"]:checked ~ .star {
    color: #ffc107;
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Character counters
    $('#title').on('input', function() {
        let remaining = 255 - $(this).val().length;
        $(this).siblings('.form-text').text(`${$(this).val().length}/255 characters`);
    });

    $('#content').on('input', function() {
        let length = $(this).val().length;
        let status = length >= 10 ? 'text-success' : 'text-muted';
        $(this).siblings('.form-text').removeClass('text-muted text-success').addClass(status);
        $(this).siblings('.form-text').text(`${length}/2000 characters (minimum 10)`);
    });

    // Form submission
    $('#review-form').on('submit', function(e) {
        e.preventDefault();
        
        let formData = new FormData(this);
        
        $.ajax({
            url: '{{ route("reviews.store") }}',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    setTimeout(() => {
                        window.location.href = '{{ route("reviews.index") }}';
                    }, 1500);
                } else {
                    toastr.error(response.message);
                }
            },
            error: function(xhr) {
                let errors = xhr.responseJSON?.errors || {};
                Object.keys(errors).forEach(function(key) {
                    toastr.error(errors[key][0]);
                });
            }
        });
    });
});
</script>
@endpush