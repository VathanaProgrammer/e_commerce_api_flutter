<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('transactions', 'coupon_id')) {
                $table->foreignId('coupon_id')->nullable()->after('user_id')->constrained()->onDelete('set null');
            }
            if (!Schema::hasColumn('transactions', 'discount_amount')) {
                $table->decimal('discount_amount', 10, 2)->default(0)->after('total_sell_price');
            }
            if (!Schema::hasColumn('transactions', 'shipping_address_id')) {
                $table->foreignId('shipping_address_id')->nullable()->after('coupon_id')->constrained('addresses')->onDelete('set null');
            }
            if (!Schema::hasColumn('transactions', 'tracking_number')) {
                $table->string('tracking_number')->nullable()->after('status');
            }
            if (!Schema::hasColumn('transactions', 'shipped_at')) {
                $table->timestamp('shipped_at')->nullable()->after('tracking_number');
            }
            if (!Schema::hasColumn('transactions', 'delivered_at')) {
                $table->timestamp('delivered_at')->nullable()->after('shipped_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['coupon_id']);
            $table->dropForeign(['shipping_address_id']);
            $table->dropColumn([
                'coupon_id',
                'discount_amount',
                'shipping_address_id',
                'tracking_number',
                'shipped_at',
                'delivered_at'
            ]);
        });
    }
};
