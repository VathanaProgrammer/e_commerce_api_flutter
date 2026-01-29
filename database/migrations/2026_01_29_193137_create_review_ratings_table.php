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
        Schema::create('review_ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('review_id')->constrained()->onDelete('cascade');
            $table->foreignId('criterion_id')->constrained('review_criteria')->onDelete('cascade');
            $table->decimal('rating', 3, 2)->default(0);
            $table->timestamps();
            
            $table->unique(['review_id', 'criterion_id'], 'review_criterion_unique');
            $table->index('review_id');
            $table->index('criterion_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('review_ratings');
    }
};
