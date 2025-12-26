<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = ['product_id', 'sku', 'price'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function attributes()
    {
        return $this->hasMany(ProductVariantAttribute::class);
    }

    public function saleLines()
    {
        return $this->hasMany(TransactionSaleLine::class);
    }
    // Many-to-Many with AttributeValue via pivot table
    public function attributeValues()
    {
        return $this->belongsToMany(
            AttributeValue::class,
            'product_variant_attributes', // pivot table
            'product_variant_id',         // FK on pivot to this model
            'attribute_value_id'          // FK on pivot to related model
        );
    }
}