<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'total_sell_price', 'total_items', 'status',
        'shipping_status', 'shipping_address', 'delivery_person', 'invoice_no', 'discount_amount'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function saleLines()
    {
        return $this->hasMany(TransactionSaleLine::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}