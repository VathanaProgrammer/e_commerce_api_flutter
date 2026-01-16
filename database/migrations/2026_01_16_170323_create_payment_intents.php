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
        Schema::create('payment_intents', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('gateway'); // aba, acleda, stripe
            $table->string('gateway_tran_id')->nullable()->unique();

            $table->decimal('amount', 15, 2);
            $table->string('currency', 3)->default('USD');

            $table->enum('status', [
                'pending',
                'success',
                'failed',
                'expired'
            ])->default('pending');

            // snapshot of cart + address + totals
            $table->json('payload_snapshot');

            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->index(['gateway', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_intents');
    }
};