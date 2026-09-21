<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class MessageDatabaseIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_messages_table_has_indexes_for_chat_queries(): void
    {
        $indexes = collect(Schema::getIndexes('messages'))
            ->keyBy('name');

        $this->assertSame(
            ['sender_id', 'receiver_id', 'id'],
            $indexes->get('messages_sender_receiver_id_index')['columns'] ?? null
        );
        $this->assertSame(
            ['receiver_id', 'sender_id', 'read_at'],
            $indexes->get('messages_receiver_sender_read_at_index')['columns'] ?? null
        );
        $this->assertSame(
            ['receiver_id', 'read_at'],
            $indexes->get('messages_receiver_read_at_index')['columns'] ?? null
        );
    }
}
