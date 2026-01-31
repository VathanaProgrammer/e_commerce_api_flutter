<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'type',
        'value',
        'min_purchase_amount',
        'max_discount_amount',
        'usage_limit',
        'used_count',
        'valid_from',
        'valid_until',
        'is_active',
        'description'
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'min_purchase_amount' => 'decimal:2',
        'max_discount_amount' => 'decimal:2',
        'usage_limit' => 'integer',
        'used_count' => 'integer',
        'valid_from' => 'datetime',
        'valid_until' => 'datetime',
        'is_active' => 'boolean'
    ];

    public function isValid()
    {
        $now = Carbon::now();
        
        return $this->is_active &&
               ($this->valid_from === null || $this->valid_from <= $now) &&
               ($this->valid_until === null || $this->valid_until >= $now) &&
               ($this->usage_limit === null || $this->used_count < $this->usage_limit);
    }

    public function calculateDiscount($subtotal)
    {
        if (!$this->isValid()) {
            return 0;
        }

        if ($this->min_purchase_amount && $subtotal < $this->min_purchase_amount) {
            return 0;
        }

        $discount = 0;

        if ($this->type === 'percentage') {
            $discount = ($subtotal * $this->value) / 100;
        } else {
            $discount = $this->value;
        }

        if ($this->max_discount_amount && $discount > $this->max_discount_amount) {
            $discount = $this->max_discount_amount;
        }

        return $discount;
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
