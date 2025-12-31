<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['category_id', 'name', 'image_url'];

    protected function imageUrl(): Attribute{
        return Attribute::make(
            get: fn ($value) =>
                    $value ? asset('uploads/products/' . $value) : asset('uploads/products/default.jpg'),
        );
    }
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function descriptionLines()
    {
        return $this->hasMany(ProductDescriptionLine::class);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    // Product.php
    public function discounts()
    {
        return $this->hasMany(ProductDiscount::class);
    }

    // Helper to get the active discount
    public function activeDiscount()
    {
        return $this->hasOne(ProductDiscount::class)->where('active', true);
    }


    // Proper attributes relationship via variants -> attribute values -> attribute
    public function attributes()
    {
        return $this->hasManyThrough(
            Attribute::class,          // Final model
            AttributeValue::class,     // Through model
            'id',                      // Foreign key on AttributeValue? We'll fix below
            'id',                      // Foreign key on Attribute
            'id',                      // Local key on Product
            'attribute_id'             // Local key on AttributeValue
        );
    }
}