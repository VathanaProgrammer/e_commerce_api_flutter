<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Users table
        Schema::create('users', function (Blueprint $table) {
            $table->id(); // default 'id'
            $table->string('email')->unique();
            $table->enum('prefix', ['Mr', 'Miss', 'other'])->nullable();
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->enum('gender', ['male','female','other'])->nullable();
            $table->string('profile_image_url')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_login')->nullable();
            $table->string('username')->nullable();
            $table->string('password_hash');
            $table->enum('role', ['admin','staff','customer'])->default('customer');
            $table->timestamps();
        });

        // Categories table
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        // Products table
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('image_url')->nullable()->default('/img/default.png');
            $table->timestamps();
        });

        // Product Description Lines
        Schema::create('product_description_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->text('text');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Attributes table
        Schema::create('attributes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        // Attribute Values table
        Schema::create('attribute_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attribute_id')->constrained()->cascadeOnDelete();
            $table->string('value');
            $table->timestamps();
        });

        // Product Variants table
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('sku')->nullable();
            $table->decimal('price', 10, 2);
            $table->timestamps();
        });

        // Product Variant Attributes table
        Schema::create('product_variant_attributes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_variant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('attribute_value_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });

        // Transactions table
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // default references 'id' in users
            $table->decimal('total_sell_price', 15, 2);
            $table->integer('total_items');
            $table->string('status');
            $table->string('shipping_status')->nullable();
            $table->text('shipping_address')->nullable();
            $table->string('delivery_person')->nullable();
            $table->string('invoice_no')->nullable();
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->timestamps();
        });

        // Transaction Sale Lines table
        Schema::create('transaction_sale_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_variant_id')->constrained()->cascadeOnDelete();
            $table->decimal('price', 10, 2);
            $table->integer('qty');
            $table->timestamps();
        });

        // Product Discounts table
        Schema::create('product_discounts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('product_variant_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('value', 10, 2);
            $table->boolean('is_percentage')->default(true);
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        // Payments table
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 15, 2);
            $table->enum('method', ['cash','acleda','aba']);
            $table->enum('status', ['pending','completed','failed'])->default('pending');
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });

        // Businesses table
        Schema::create('businesses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('logo')->nullable();
            $table->string('mobile')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
        Schema::dropIfExists('product_discounts');
        Schema::dropIfExists('transaction_sale_lines');
        Schema::dropIfExists('transactions');
        Schema::dropIfExists('product_variant_attributes');
        Schema::dropIfExists('product_variants');
        Schema::dropIfExists('attribute_values');
        Schema::dropIfExists('attributes');
        Schema::dropIfExists('product_description_lines');
        Schema::dropIfExists('products');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('users');
        Schema::dropIfExists('businesses');
    }
};