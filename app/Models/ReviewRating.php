<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReviewRating extends Model
{
    use HasFactory;

    protected $fillable = [
        'review_id',
        'criterion_id',
        'rating',
    ];

    protected $casts = [
        'rating' => 'decimal:2',
    ];

    public function review(): BelongsTo
    {
        return $this->belongsTo(Review::class);
    }

    public function criterion(): BelongsTo
    {
        return $this->belongsTo(ReviewCriterion::class);
    }

    public function getStarRatingAttribute(): string
    {
        $rating = round($this->rating);
        return str_repeat('★', $rating) . str_repeat('☆', 5 - $rating);
    }
}
