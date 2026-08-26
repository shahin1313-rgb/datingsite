<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('premium_payment_intents', function (Blueprint $table) {
            $table->id();

            $table->uuid('public_id')->unique();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->unsignedBigInteger('reference_code')->unique();
            $table->string('expected_amount_atomic', 78)->unique();

            $table->timestamp('expires_at');
            $table->timestamp('consumed_at')->nullable();
            $table->timestamps();

            $table->index(
                ['user_id', 'consumed_at', 'expires_at'],
                'premium_intents_active_lookup'
            );
        });

        Schema::table('premium_payments', function (Blueprint $table) {
            $table->foreignId('payment_intent_id')
                ->nullable()
                ->after('user_id')
                ->unique()
                ->constrained('premium_payment_intents')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('premium_payments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('payment_intent_id');
        });

        Schema::dropIfExists('premium_payment_intents');
    }
};
