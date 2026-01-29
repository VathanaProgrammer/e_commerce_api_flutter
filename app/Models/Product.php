<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['category_id', 'name', 'image_url', 'is_recommended', 'is_featured', "active"];

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    protected function imageUrl(): Attribute
    {
        return Attribute::make(
            get: fn($value) =>
            $value ? asset($value) : asset('uploads/products/default.jpg'),
        );
    }
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function descriptionLines()
    {
        return $this->hasMany(ProductDescriptionLine::class);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    // Product.php
    public function discounts()
    {
        return $this->hasMany(ProductDiscount::class);
    }

    // Helper to get the active discount
    public function activeDiscount()
    {
        return $this->hasOne(ProductDiscount::class)->where('active', true);
    }


    // Proper attributes relationship via variants -> attribute values -> attribute
    public function attributes()
    {
        return $this->hasManyThrough(
            Attribute::class,          // Final model
            AttributeValue::class,     // Through model
            'id',                      // Foreign key on AttributeValue? We'll fix below
            'id',                      // Foreign key on Attribute
            'id',                      // Local key on Product
            'attribute_id'             // Local key on AttributeValue
        );
    }

    public function favoritedBy()
    {
        return $this->belongsToMany(User::class, 'favorites');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function approvedReviews()
    {
        return $this->reviews()->approved();
    }

    public function verifiedReviews()
    {
        return $this->reviews()->approved()->verified();
    }

    public function getAverageRatingAttribute(): float
    {
        return $this->approvedReviews()->avg('overall_rating') ?? 0;
    }

    public function getTotalReviewsAttribute(): int
    {
        return $this->approvedReviews()->count();
    }

    public function getVerifiedReviewsCountAttribute(): int
    {
        return $this->verifiedReviews()->count();
    }

    public function getRatingBreakdownAttribute(): array
    {
        $breakdown = [];
        for ($i = 1; $i <= 5; $i++) {
            $breakdown[$i] = $this->approvedReviews()
                ->where('overall_rating', '>=', $i)
                ->where('overall_rating', '<', $i + 1)
                ->count();
        }
        return $breakdown;
    }

    public function getStarRatingAttribute(): string
    {
        $rating = round($this->average_rating);
        return str_repeat('★', $rating) . str_repeat('☆', 5 - $rating);
    }

    public function updateRatingCounts(): void
    {
        $this->save();
    }
}