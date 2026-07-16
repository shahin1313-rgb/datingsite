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
        Schema::table('users', function (Blueprint $table) {
            // وضعیت پرمیوم کاربر (به صورت پیش‌فرض صفر یا false)
        $table->boolean('is_premium')->default(false)->after('email');
        // ذخیره آخرین هش تراکنش ثبت شده برای بررسی‌های مالی
        $table->string('last_crypto_hash')->nullable()->after('is_premium');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['is_premium', 'last_crypto_hash']);
        });
    }
};
