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
        Schema::create('payments', function (Blueprint $table) {
             $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // payer
            $table->string('gateway')->nullable();
            $table->string('gateway_transaction_id')->nullable();
            $table->integer('amount'); // amount in smallest currency unit (e.g. toman)
            $table->string('status')->default('pending'); // pending, succeeded, failed
            $table->json('meta')->nullable(); // هر اطلاعات اضافی
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
