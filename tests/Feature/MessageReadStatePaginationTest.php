<?php

namespace Tests\Feature;

use App\Models\Message;
use App\Models\User;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class MessageReadStatePaginationTest extends TestCase
{
    use DatabaseTransactions;

    public function test_opening_conversation_clears_read_at_unread_count(): void
    {
        $viewer = User::factory()->create();
        $contact = User::factory()->create();

        Message::create([
            'sender_id' => $contact->id,
            'receiver_id' => $viewer->id,
            'message' => 'قبلاً خوانده شده',
            'read_at' => now()->subMinute(),
            'status' => 'sent',
        ]);

        $unread = Message::create([
            'sender_id' => $contact->id,
            'receiver_id' => $viewer->id,
            'message' => 'پیام جدید',
            'status' => 'sent',
        ]);

        $this->actingAs($viewer)
            ->get(route('messages.index'))
            ->assertOk()
            ->assertViewHas(
                'unreadCounts',
                fn (array $counts): bool =>
                    ($counts[$contact->id] ?? null) === 1
            );

        $this->actingAs($viewer)
            ->get(route('messages.show', $contact))
            ->assertOk();

        $this->assertNotNull($unread->fresh()->read_at);

        $this->actingAs($viewer)
            ->get(route('messages.index'))
            ->assertOk()
            ->assertViewHas(
                'unreadCounts',
                fn (array $counts): bool =>
                    ($counts[$contact->id] ?? null) === 0
            );
    }

    public function test_conversation_history_is_paginated_latest_first(): void
    {
        $viewer = User::factory()->create();
        $contact = User::factory()->create();
        $ids = [];

        for ($number = 1; $number <= 55; $number++) {
            $ids[] = Message::create([
                'sender_id' => $viewer->id,
                'receiver_id' => $contact->id,
                'message' => 'پیام شماره '.$number,
                'status' => 'sent',
            ])->id;
        }

        $this->actingAs($viewer)
            ->get(route('messages.show', $contact))
            ->assertOk()
            ->assertViewHas(
                'messages',
                function (Paginator $messages) use ($ids): bool {
                    return $messages->count() === 50
                        && $messages->hasMorePages()
                        && $messages->first()->id === $ids[5]
                        && $messages->last()->id === $ids[54];
                }
            );

        $this->actingAs($viewer)
            ->get(route('messages.show', [
                'user' => $contact,
                'page' => 2,
            ]))
            ->assertOk()
            ->assertViewHas(
                'messages',
                fn (Paginator $messages): bool =>
                    $messages->count() === 5
                    && $messages->first()->id === $ids[0]
                    && $messages->last()->id === $ids[4]
            );
    }

    public function test_only_read_at_remains_as_message_read_state(): void
    {
        $this->assertTrue(
            Schema::hasColumn('messages', 'read_at')
        );
        $this->assertFalse(
            Schema::hasColumn('messages', 'is_read')
        );
        $this->assertFalse(
            Schema::hasColumn('messages', 'is_seen')
        );
        $this->assertFalse(
            Schema::hasColumn('messages', 'seen_at')
        );
    }
}
