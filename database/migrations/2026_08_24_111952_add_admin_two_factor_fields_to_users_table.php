<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table
                ->string('admin_two_factor_code_hash')
                ->nullable();

            $table
                ->timestamp('admin_two_factor_expires_at')
                ->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'admin_two_factor_code_hash',
                'admin_two_factor_expires_at',
            ]);
        });
    }
};