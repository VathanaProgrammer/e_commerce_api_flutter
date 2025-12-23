<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionSaleLine extends Model
{
    use HasFactory;

    protected $fillable = ['transaction_id', 'product_variant_id', 'price', 'qty'];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }
}