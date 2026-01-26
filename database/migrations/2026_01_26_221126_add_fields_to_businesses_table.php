<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('businesses', function (Blueprint $table) {
            $table->string('email')->nullable()->after('mobile');
            $table->string('address')->nullable()->after('email');
            $table->string('city')->nullable()->after('address');
            $table->string('country')->nullable()->after('city');
            $table->string('postal_code')->nullable()->after('country');
            $table->string('currency', 10)->default('USD')->after('postal_code');
            $table->string('currency_symbol', 5)->default('$')->after('currency');
            $table->decimal('tax_rate', 5, 2)->default(0)->after('currency_symbol');
            $table->string('tax_name', 50)->default('VAT')->after('tax_rate');
            $table->boolean('tax_enabled')->default(false)->after('tax_name');
            $table->string('timezone')->default('UTC')->after('tax_enabled');
            $table->string('date_format', 20)->default('Y-m-d')->after('timezone');
            $table->string('time_format', 20)->default('H:i')->after('date_format');
            $table->text('footer_text')->nullable()->after('time_format');
            $table->string('website')->nullable()->after('footer_text');
            $table->string('facebook')->nullable()->after('website');
            $table->string('instagram')->nullable()->after('facebook');
            $table->string('telegram')->nullable()->after('instagram');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('businesses', function (Blueprint $table) {
            $table->dropColumn([
                'email', 'address', 'city', 'country', 'postal_code',
                'currency', 'currency_symbol', 'tax_rate', 'tax_name', 'tax_enabled',
                'timezone', 'date_format', 'time_format', 'footer_text',
                'website', 'facebook', 'instagram', 'telegram'
            ]);
        });
    }
};
