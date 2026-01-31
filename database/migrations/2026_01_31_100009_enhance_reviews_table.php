<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Check if reviews table exists and add missing columns
        if (Schema::hasTable('reviews')) {
            Schema::table('reviews', function (Blueprint $table) {
                if (!Schema::hasColumn('reviews', 'images')) {
                    $table->json('images')->nullable();
                }
                if (!Schema::hasColumn('reviews', 'helpful_count')) {
                    $table->integer('helpful_count')->default(0);
                }
                if (!Schema::hasColumn('reviews', 'verified_purchase')) {
                    $table->boolean('verified_purchase')->default(false);
                }
                if (!Schema::hasColumn('reviews', 'is_approved')) {
                    $table->boolean('is_approved')->default(true);
                }
                if (!Schema::hasColumn('reviews', 'approved_at')) {
                    $table->timestamp('approved_at')->nullable();
                }
            });
        } else {
            // Create reviews table if it doesn't exist
            Schema::create('reviews', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->foreignId('product_id')->constrained()->onDelete('cascade');
                $table->foreignId('transaction_id')->nullable()->constrained()->onDelete('set null');
                $table->integer('rating'); // 1-5
                $table->string('title')->nullable();
                $table->text('content')->nullable();
                $table->json('images')->nullable();
                $table->integer('helpful_count')->default(0);
                $table->boolean('verified_purchase')->default(false);
                $table->boolean('is_approved')->default(true);
                $table->timestamp('approved_at')->nullable();
                $table->timestamps();

                $table->index(['product_id', 'is_approved']);
                $table->index(['user_id', 'product_id']);
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('reviews')) {
            Schema::table('reviews', function (Blueprint $table) {
                $table->dropColumn(['images', 'helpful_count', 'verified_purchase', 'is_approved', 'approved_at']);
            });
        }
    }
};
