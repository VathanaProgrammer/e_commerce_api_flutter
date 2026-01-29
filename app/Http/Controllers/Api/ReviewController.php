<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\Product;
use App\Models\ReviewCriterion;
use App\Models\ReviewRating;
use App\Models\ReviewHelpfulVote;
use App\Http\Requests\ReviewRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReviewController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Review::with(['user', 'product', 'ratings.criterion'])
            ->approved();

        if ($request->has('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('rating')) {
            $query->where('overall_rating', '>=', $request->rating)
                  ->where('overall_rating', '<', $request->rating + 1);
        }

        if ($request->has('verified_only') && $request->boolean('verified_only')) {
            $query->verified();
        }

        if ($request->has('featured_only') && $request->boolean('featured_only')) {
            $query->featured();
        }

        if ($request->has('sort')) {
            switch ($request->sort) {
                case 'helpful':
                    $query->orderByDesc('helpful_count');
                    break;
                case 'rating_high':
                    $query->orderByDesc('overall_rating');
                    break;
                case 'rating_low':
                    $query->orderBy('overall_rating');
                    break;
                case 'recent':
                default:
                    $query->latest();
                    break;
            }
        } else {
            $query->latest();
        }

        $reviews = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $reviews
        ]);
    }

    public function store(ReviewRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $product = Product::findOrFail($request->product_id);

            $existingReview = Review::where('user_id', Auth::id())
                ->where('product_id', $request->product_id)
                ->first();

            if ($existingReview) {
                return response()->json([
                    'success' => false,
                    'message' => 'You have already reviewed this product.'
                ], 422);
            }

            $review = Review::create([
                'user_id' => Auth::id(),
                'product_id' => $request->product_id,
                'title' => $request->title,
                'content' => $request->content,
                'overall_rating' => $request->overall_rating,
                'is_verified_purchase' => $this->checkVerifiedPurchase($request->product_id),
                'is_approved' => true,
            ]);

            if ($request->has('ratings') && is_array($request->ratings)) {
                foreach ($request->ratings as $criterionId => $rating) {
                    ReviewRating::create([
                        'review_id' => $review->id,
                        'criterion_id' => $criterionId,
                        'rating' => $rating,
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Review submitted successfully!',
                'data' => $review->load(['user', 'ratings.criterion'])
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Error submitting review: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(Review $review): JsonResponse
    {
        $review->load(['user', 'product', 'ratings.criterion', 'helpfulVotes']);

        return response()->json([
            'success' => true,
            'data' => $review
        ]);
    }

    public function update(ReviewRequest $request, Review $review): JsonResponse
    {
        if ($review->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to update this review.'
            ], 403);
        }

        try {
            DB::beginTransaction();

            $review->update([
                'title' => $request->title,
                'content' => $request->content,
                'overall_rating' => $request->overall_rating,
            ]);

            if ($request->has('ratings') && is_array($request->ratings)) {
                $review->ratings()->delete();
                
                foreach ($request->ratings as $criterionId => $rating) {
                    ReviewRating::create([
                        'review_id' => $review->id,
                        'criterion_id' => $criterionId,
                        'rating' => $rating,
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Review updated successfully!',
                'data' => $review->load(['ratings.criterion'])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Error updating review: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Review $review): JsonResponse
    {
        if ($review->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to delete this review.'
            ], 403);
        }

        try {
            $review->delete();

            return response()->json([
                'success' => true,
                'message' => 'Review deleted successfully!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting review: ' . $e->getMessage()
            ], 500);
        }
    }

    public function voteHelpful(Request $request, Review $review): JsonResponse
    {
        $request->validate([
            'is_helpful' => 'required|boolean'
        ]);

        $user = Auth::user();
        $ipAddress = $request->ip();

        if (ReviewHelpfulVote::hasVoted($review->id, $user?->id, $ipAddress)) {
            return response()->json([
                'success' => false,
                'message' => 'You have already voted on this review.'
            ], 422);
        }

        ReviewHelpfulVote::create([
            'review_id' => $review->id,
            'user_id' => $user?->id,
            'is_helpful' => $request->is_helpful,
            'ip_address' => $ipAddress,
            'user_agent' => $request->userAgent(),
        ]);

        $review->updateHelpfulCounts();

        return response()->json([
            'success' => true,
            'message' => 'Vote recorded successfully!',
            'data' => [
                'helpful_count' => $review->helpful_count,
                'total_votes' => $review->total_votes,
                'helpful_percentage' => $review->helpful_percentage,
            ]
        ]);
    }

    public function productReviews(Product $product, Request $request): JsonResponse
    {
        $query = $product->approvedReviews()
            ->with(['user', 'ratings.criterion']);

        if ($request->has('rating')) {
            $query->where('overall_rating', '>=', $request->rating)
                  ->where('overall_rating', '<', $request->rating + 1);
        }

        if ($request->has('verified_only') && $request->boolean('verified_only')) {
            $query->verified();
        }

        if ($request->has('sort')) {
            switch ($request->sort) {
                case 'helpful':
                    $query->orderByDesc('helpful_count');
                    break;
                case 'rating_high':
                    $query->orderByDesc('overall_rating');
                    break;
                case 'rating_low':
                    $query->orderBy('overall_rating');
                    break;
                case 'recent':
                default:
                    $query->latest();
                    break;
            }
        } else {
            $query->latest();
        }

        $reviews = $query->paginate($request->get('per_page', 10));

        $stats = [
            'average_rating' => $product->average_rating,
            'total_reviews' => $product->total_reviews,
            'verified_reviews_count' => $product->verified_reviews_count,
            'rating_breakdown' => $product->rating_breakdown,
        ];

        return response()->json([
            'success' => true,
            'data' => $reviews,
            'stats' => $stats
        ]);
    }

    public function userReviews(Request $request): JsonResponse
    {
        $reviews = Review::where('user_id', Auth::id())
            ->with(['product', 'ratings.criterion'])
            ->latest()
            ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $reviews
        ]);
    }

    public function criteria(): JsonResponse
    {
        $criteria = ReviewCriterion::active()->ordered()->get();

        return response()->json([
            'success' => true,
            'data' => $criteria
        ]);
    }

    private function checkVerifiedPurchase(int $productId): bool
    {
        $user = Auth::user();
        
        if (!$user) {
            return false;
        }

        return $user->transactions()
            ->whereHas('saleLines.product', fn($query) => $query->where('product_id', $productId))
            ->exists();
    }
}
