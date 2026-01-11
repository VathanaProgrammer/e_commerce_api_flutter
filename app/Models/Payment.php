<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\PaymentStatus;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = ['transaction_id', 'amount', 'method', 'status', 'paid_at'];
    
    protected $casts = [
        'amount' => 'decimal:2',
        'status' => PaymentStatus::class, 
        'paid_at' => 'datetime',
    ];

    protected $attributes = [
        'status' => 'pending',
    ];
    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}