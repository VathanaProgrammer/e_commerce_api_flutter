<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\TransactionStatus;
use App\Enums\ShippingStatus;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'total_sell_price', 'total_items', 'status', 'lat', 'long',
        'shipping_status', 'shipping_address', 'delivery_person','shipping_charge', 'invoice_no', 'discount_amount'
    ];

    protected $casts = [
        'total_sell_price' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'status' => TransactionStatus::class,          
        'shipping_status' => ShippingStatus::class,    
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
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