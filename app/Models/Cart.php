<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_variant_id',
        'quantity',
        'price_at_addition',
        'session_id'
    ];

    protected $casts = [
        'price_at_addition' => 'decimal:2',
        'quantity' => 'integer'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class);
    }

    public function product()
    {
        return $this->hasOneThrough(
            Product::class,
            ProductVariant::class,
            'id',
            'id',
            'product_variant_id',
            'product_id'
        );
    }

    public function getTotalAttribute()
    {
        return $this->quantity * $this->price_at_addition;
    }
}
