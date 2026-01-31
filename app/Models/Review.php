<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
        'transaction_id',
        'rating',
        'title',
        'comment',
        'images',
        'helpful_count',
        'verified_purchase',
        'is_approved',
        'approved_at'
    ];

    protected $casts = [
        'rating' => 'integer',
        'images' => 'array',
        'helpful_count' => 'integer',
        'verified_purchase' => 'boolean',
        'is_approved' => 'boolean',
        'approved_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function helpfulVotes()
    {
        return $this->hasMany(ReviewHelpful::class);
    }

    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    public function scopeVerifiedPurchase($query)
    {
        return $query->where('verified_purchase', true);
    }
}
