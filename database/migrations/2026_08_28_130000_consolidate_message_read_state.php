<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $hasIsRead = Schema::hasColumn('messages', 'is_read');
        $hasIsSeen = Schema::hasColumn('messages', 'is_seen');
        $hasSeenAt = Schema::hasColumn('messages', 'seen_at');

        if ($hasSeenAt) {
            DB::table('messages')
                ->whereNull('read_at')
                ->whereNotNull('seen_at')
                ->update([
                    'read_at' => DB::raw('seen_at'),
                ]);
        }

        if ($hasIsRead || $hasIsSeen) {
            DB::table('messages')
                ->whereNull('read_at')
                ->where(function ($query) use (
                    $hasIsRead,
                    $hasIsSeen
                ): void {
                    if ($hasIsRead) {
                        $query->where('is_read', true);
                    }

                    if ($hasIsSeen) {
                        $method = $hasIsRead
                            ? 'orWhere'
                            : 'where';

                        $query->{$method}('is_seen', true);
                    }
                })
                ->update([
                    'read_at' => DB::raw(
                        'COALESCE(updated_at, CURRENT_TIMESTAMP)'
                    ),
                ]);
        }

        $legacyColumns = array_values(array_filter([
            $hasIsRead ? 'is_read' : null,
            $hasIsSeen ? 'is_seen' : null,
            $hasSeenAt ? 'seen_at' : null,
        ]));

        if ($legacyColumns !== []) {
            Schema::table(
                'messages',
                fn (Blueprint $table) =>
                    $table->dropColumn($legacyColumns)
            );
        }
    }

    public function down(): void
    {
        $missingLegacyColumns = array_values(array_filter([
            Schema::hasColumn('messages', 'is_read') ? null : 'is_read',
            Schema::hasColumn('messages', 'is_seen') ? null : 'is_seen',
            Schema::hasColumn('messages', 'seen_at') ? null : 'seen_at',
        ]));

        if ($missingLegacyColumns !== []) {
            Schema::table(
                'messages',
                function (Blueprint $table) use ($missingLegacyColumns): void {
                    if (in_array('is_read', $missingLegacyColumns, true)) {
                        $table->boolean('is_read')->default(false);
                    }

                    if (in_array('is_seen', $missingLegacyColumns, true)) {
                        $table->boolean('is_seen')->default(false);
                    }

                    if (in_array('seen_at', $missingLegacyColumns, true)) {
                        $table->timestamp('seen_at')->nullable();
                    }
                }
            );
        }

        DB::table('messages')
            ->whereNotNull('read_at')
            ->update([
                'is_read' => true,
                'is_seen' => true,
                'seen_at' => DB::raw('read_at'),
            ]);
    }
};
