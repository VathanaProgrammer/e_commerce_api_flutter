<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Coupon;
use Carbon\Carbon;

class CouponSeeder extends Seeder
{
    public function run(): void
    {
        $coupons = [
            [
                'code' => 'WELCOME10',
                'type' => 'percentage',
                'value' => 10.00,
                'min_purchase_amount' => 50.00,
                'max_discount_amount' => 20.00,
                'usage_limit' => 100,
                'used_count' => 0,
                'valid_from' => Carbon::now(),
                'valid_until' => Carbon::now()->addMonths(3),
                'is_active' => true,
                'description' => 'Welcome discount - 10% off on orders above $50'
            ],
            [
                'code' => 'SAVE20',
                'type' => 'fixed',
                'value' => 20.00,
                'min_purchase_amount' => 100.00,
                'max_discount_amount' => null,
                'usage_limit' => 50,
                'used_count' => 0,
                'valid_from' => Carbon::now(),
                'valid_until' => Carbon::now()->addMonths(2),
                'is_active' => true,
                'description' => 'Save $20 on orders above $100'
            ],
            [
                'code' => 'FLASH15',
                'type' => 'percentage',
                'value' => 15.00,
                'min_purchase_amount' => 75.00,
                'max_discount_amount' => 30.00,
                'usage_limit' => 200,
                'used_count' => 0,
                'valid_from' => Carbon::now(),
                'valid_until' => Carbon::now()->addDays(7),
                'is_active' => true,
                'description' => 'Flash sale - 15% off for limited time'
            ],
            [
                'code' => 'FREESHIP',
                'type' => 'fixed',
                'value' => 5.00,
                'min_purchase_amount' => 30.00,
                'max_discount_amount' => null,
                'usage_limit' => null,
                'used_count' => 0,
                'valid_from' => Carbon::now(),
                'valid_until' => null,
                'is_active' => true,
                'description' => 'Free shipping on orders above $30'
            ],
            [
                'code' => 'VIP25',
                'type' => 'percentage',
                'value' => 25.00,
                'min_purchase_amount' => 200.00,
                'max_discount_amount' => 50.00,
                'usage_limit' => 20,
                'used_count' => 0,
                'valid_from' => Carbon::now(),
                'valid_until' => Carbon::now()->addMonths(1),
                'is_active' => true,
                'description' => 'VIP discount - 25% off on orders above $200'
            ]
        ];

        foreach ($coupons as $coupon) {
            Coupon::create($coupon);
        }
    }
}
