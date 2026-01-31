<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function validate(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'subtotal' => 'required|numeric|min:0'
        ]);

        $coupon = Coupon::where('code', $request->code)->first();

        if (!$coupon) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid coupon code'
            ], 404);
        }

        if (!$coupon->isValid()) {
            return response()->json([
                'success' => false,
                'message' => 'This coupon is no longer valid'
            ], 400);
        }

        $discount = $coupon->calculateDiscount($request->subtotal);

        if ($discount == 0) {
            return response()->json([
                'success' => false,
                'message' => 'Minimum purchase amount not met'
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'Coupon applied successfully',
            'data' => [
                'coupon' => $coupon,
                'discount_amount' => $discount,
                'final_amount' => max(0, $request->subtotal - $discount)
            ]
        ]);
    }

    public function index()
    {
        $coupons = Coupon::where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('valid_until')
                    ->orWhere('valid_until', '>=', now());
            })
            ->get();

        return response()->json([
            'success' => true,
            'data' => $coupons
        ]);
    }
}
