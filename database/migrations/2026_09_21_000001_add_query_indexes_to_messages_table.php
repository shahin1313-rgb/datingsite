<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('messages', function (Blueprint $table): void {
            $table->index(
                ['sender_id', 'receiver_id', 'id'],
                'messages_sender_receiver_id_index'
            );
            $table->index(
                ['receiver_id', 'sender_id', 'read_at'],
                'messages_receiver_sender_read_at_index'
            );
            $table->index(
                ['receiver_id', 'read_at'],
                'messages_receiver_read_at_index'
            );
        });
    }

    public function down(): void
    {
        $indexes = collect(Schema::getIndexes('messages'));

        $hasSenderFallback = $indexes->contains(
            static fn (array $index): bool =>
                ($index['columns'][0] ?? null) === 'sender_id'
                && $index['name'] !== 'messages_sender_receiver_id_index'
        );

        $hasReceiverFallback = $indexes->contains(
            static fn (array $index): bool =>
                ($index['columns'][0] ?? null) === 'receiver_id'
                && ! in_array(
                    $index['name'],
                    [
                        'messages_receiver_sender_read_at_index',
                        'messages_receiver_read_at_index',
                    ],
                    true
                )
        );

        Schema::table('messages', function (Blueprint $table) use (
            $hasSenderFallback,
            $hasReceiverFallback
        ): void {
            /*
             * InnoDB may discard the original implicit indexes after the
             * composite indexes are added. Recreate supporting indexes before
             * dropping the composites so the foreign keys remain valid.
             */
            if (! $hasSenderFallback) {
                $table->index(
                    'sender_id',
                    'messages_sender_id_rollback_index'
                );
            }

            if (! $hasReceiverFallback) {
                $table->index(
                    'receiver_id',
                    'messages_receiver_id_rollback_index'
                );
            }

            $table->dropIndex('messages_sender_receiver_id_index');
            $table->dropIndex('messages_receiver_sender_read_at_index');
            $table->dropIndex('messages_receiver_read_at_index');
        });
    }
};
