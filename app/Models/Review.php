<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
        'transaction_id',
        'title',
        'content',
        'overall_rating',
        'is_verified_purchase',
        'is_approved',
        'is_featured',
        'helpful_count',
        'total_votes',
        'admin_response',
        'admin_response_date',
        'responded_by',
    ];

    protected $casts = [
        'overall_rating' => 'decimal:2',
        'is_verified_purchase' => 'boolean',
        'is_approved' => 'boolean',
        'is_featured' => 'boolean',
        'admin_response_date' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    public function respondedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responded_by');
    }

    public function ratings(): HasMany
    {
        return $this->hasMany(ReviewRating::class);
    }

    public function helpfulVotes(): HasMany
    {
        return $this->hasMany(ReviewHelpfulVote::class);
    }

    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified_purchase', true);
    }

    public function getHelpfulPercentageAttribute(): float
    {
        if ($this->total_votes === 0) {
            return 0;
        }

        return round(($this->helpful_count / $this->total_votes) * 100, 2);
    }

    public function updateHelpfulCounts(): void
    {
        $this->helpful_count = $this->helpfulVotes()->where('is_helpful', true)->count();
        $this->total_votes = $this->helpfulVotes()->count();
        $this->save();
    }

    public function canBeEditedBy(?User $user): bool
    {
        if (!$user) {
            return false;
        }

        return $this->user_id === $user->id || $user->hasRole('admin');
    }

    public function getStarRatingAttribute(): string
    {
        $rating = round($this->overall_rating);
        return str_repeat('★', $rating) . str_repeat('☆', 5 - $rating);
    }
}
