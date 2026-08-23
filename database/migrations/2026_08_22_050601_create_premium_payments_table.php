<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('premium_payments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('network', 32);
            $table->unsignedBigInteger('chain_id');
            $table->string('asset', 20);
            $table->char('token_contract', 42);

            $table->char('tx_hash', 66)->unique();

            $table->char('sender_address', 42);
            $table->char('receiver_address', 42);
            $table->string('amount_atomic', 78);

            $table->unsignedBigInteger('block_number');
            $table->unsignedInteger('confirmations');

            $table->string('status', 20)
                ->default('verified');

            $table->timestamp('verified_at');
            $table->timestamps();

            $table->index([
                'user_id',
                'created_at',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('premium_payments');
    }
};