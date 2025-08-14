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
        Schema::create('likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // کسی که لایک می‌کند
            $table->foreignId('liked_user_id')->constrained('users')->onDelete('cascade'); // کسی که لایک می‌شود
            $table->timestamps();

            $table->unique(['user_id', 'liked_user_id']); // هر کاربر فقط یکبار لایک کند
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('likes');
    }
};
