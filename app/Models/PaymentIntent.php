<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentIntent extends Model
{
    //
    protected $table = "payment_intents";

    protected $fillable = [
        'user_id',
        'gateway',
        'gateway_tran_id',
        'amount',
        'currency',
        'status',
        'payload_snapshot',
        'expires_at',
    ];

    protected $casts = [
        'payload_snapshot' => 'array',
        'expires_at' => 'datetime',
    ];

    /**
     * Relationship: PaymentIntent belongs to a user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}