<?php

namespace App\Http\Controllers;

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
use Yajra\DataTables\Facades\DataTables;

class ReviewController extends Controller
{
    public function index()
    {
        $reviews = Review::with(['user', 'product', 'ratings.criterion'])
            ->latest()
            ->paginate(20);

        return view('reviews.index', compact('reviews'));
    }

    public function create()
    {
        $products = Product::active()->get();
        $criteria = ReviewCriterion::active()->ordered()->get();

        return view('reviews.create', compact('products', 'criteria'));
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
                'review' => $review->load(['ratings.criterion'])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Error submitting review: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(Review $review)
    {
        $review->load(['user', 'product', 'ratings.criterion', 'helpfulVotes']);

        return view('reviews.show', compact('review'));
    }

    public function edit(Review $review)
    {
        $this->authorize('update', $review);

        $review->load(['ratings.criterion']);
        $criteria = ReviewCriterion::active()->ordered()->get();

        return view('reviews.edit', compact('review', 'criteria'));
    }

    public function update(ReviewRequest $request, Review $review): JsonResponse
    {
        $this->authorize('update', $review);

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
                'review' => $review->load(['ratings.criterion'])
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
        $this->authorize('delete', $review);

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

    public function approve(Review $review): JsonResponse
    {
        $this->authorize('moderate', Review::class);

        $review->update(['is_approved' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Review approved successfully!'
        ]);
    }

    public function reject(Review $review): JsonResponse
    {
        $this->authorize('moderate', Review::class);

        $review->update(['is_approved' => false]);

        return response()->json([
            'success' => true,
            'message' => 'Review rejected successfully!'
        ]);
    }

    public function feature(Review $review): JsonResponse
    {
        $this->authorize('moderate', Review::class);

        $review->update(['is_featured' => !$review->is_featured]);

        return response()->json([
            'success' => true,
            'message' => $review->is_featured ? 'Review featured!' : 'Review unfeatured!'
        ]);
    }

    public function respond(Request $request, Review $review): JsonResponse
    {
        $this->authorize('moderate', Review::class);

        $request->validate([
            'response' => 'required|string|max:1000'
        ]);

        $review->update([
            'admin_response' => $request->response,
            'admin_response_date' => now(),
            'responded_by' => Auth::id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Response added successfully!'
        ]);
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
            'helpful_count' => $review->helpful_count,
            'total_votes' => $review->total_votes,
            'helpful_percentage' => $review->helpful_percentage,
        ]);
    }

    public function getProductReviews(Product $product): JsonResponse
    {
        $reviews = $product->approvedReviews()
            ->with(['user', 'ratings.criterion'])
            ->latest()
            ->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $reviews,
            'average_rating' => $product->average_rating,
            'total_reviews' => $product->total_reviews,
            'rating_breakdown' => $product->rating_breakdown,
        ]);
    }

    public function getDataTable(): JsonResponse
    {
        $reviews = Review::with(['user', 'product', 'ratings']);

        return DataTables::of($reviews)
            ->addColumn('user_name', fn($review) => $review->user->name)
            ->addColumn('product_name', fn($review) => $review->product->name)
            ->addColumn('overall_rating', fn($review) => $review->star_rating)
            ->addColumn('status', fn($review) => $review->is_approved ? 'Approved' : 'Pending')
            ->addColumn('actions', function($review) {
                $actions = '';
                if (auth()->user()->can('update', $review)) {
                    $actions .= '<button class="btn btn-sm btn-primary edit-review" data-id="' . $review->id . '">Edit</button>';
                }
                if (auth()->user()->can('delete', $review)) {
                    $actions .= '<button class="btn btn-sm btn-danger delete-review" data-id="' . $review->id . '">Delete</button>';
                }
                if (auth()->user()->can('moderate', Review::class)) {
                    $actions .= '<button class="btn btn-sm btn-success approve-review" data-id="' . $review->id . '">Approve</button>';
                    $actions .= '<button class="btn btn-sm btn-warning reject-review" data-id="' . $review->id . '">Reject</button>';
                }
                return $actions;
            })
            ->rawColumns(['actions', 'overall_rating'])
            ->make(true);
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
