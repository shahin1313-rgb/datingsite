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
        Schema::create('user_profile_visits', function (Blueprint $table) {
            $table->id();
    $table->unsignedBigInteger('viewer_id'); // کسی که بازدید کرده
    $table->unsignedBigInteger('profile_owner_id'); // صاحب پروفایل
    $table->timestamps();

    $table->foreign('viewer_id')->references('id')->on('users')->onDelete('cascade');
    $table->foreign('profile_owner_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_profile_visits');
    }
};
