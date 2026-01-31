<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Newsletter extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'name',
        'is_subscribed',
        'subscribed_at',
        'unsubscribed_at',
        'verification_token',
        'verified_at'
    ];

    protected $casts = [
        'is_subscribed' => 'boolean',
        'subscribed_at' => 'datetime',
        'unsubscribed_at' => 'datetime',
        'verified_at' => 'datetime'
    ];

    public function scopeSubscribed($query)
    {
        return $query->where('is_subscribed', true);
    }

    public function scopeVerified($query)
    {
        return $query->whereNotNull('verified_at');
    }
}
