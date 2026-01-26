<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Business extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'logo',
        'mobile',
        'email',
        'address',
        'city',
        'country',
        'postal_code',
        'currency',
        'currency_symbol',
        'tax_rate',
        'tax_name',
        'tax_enabled',
        'timezone',
        'date_format',
        'time_format',
        'footer_text',
        'website',
        'facebook',
        'instagram',
        'telegram',
    ];

    protected $appends = ['logo_url'];

    protected $casts = [
        'tax_enabled' => 'boolean',
        'tax_rate' => 'decimal:2',
    ];

    /**
     * Get the logo URL
     */
    public function getLogoUrlAttribute(): ?string
    {
        if ($this->logo) {
            return asset('uploads/business/' . $this->logo);
        }
        return null;
    }

    /**
     * Format currency amount
     */
    public function formatCurrency($amount): string
    {
        return $this->currency_symbol . number_format($amount, 2);
    }

    /**
     * Calculate tax for an amount
     */
    public function calculateTax($amount): float
    {
        if (!$this->tax_enabled) {
            return 0;
        }
        return $amount * ($this->tax_rate / 100);
    }
}