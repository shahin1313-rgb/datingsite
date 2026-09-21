<?php

namespace Tests\Feature;

use App\Models\Message;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class MessageFirstMessageLockTest extends TestCase
{
    use DatabaseTransactions;

    public function test_only_one_free_first_message_is_allowed_per_pair(): void
    {
        $firstUser = User::factory()->create();
        $secondUser = User::factory()->create();

        $this->actingAs($firstUser)
            ->postJson(route('messages.store'), [
                'receiver_id' => $secondUser->id,
                'message' => 'پیام اول',
            ])
            ->assertOk()
            ->assertJson([
                'success' => true,
                'type' => 'FIRST_MESSAGE_PRIVATE',
            ]);

        /*
         * Checking the reverse direction verifies that the lock and the
         * existence query use an unordered participant pair.
         */
        $this->actingAs($secondUser)
            ->postJson(route('messages.store'), [
                'receiver_id' => $firstUser->id,
                'message' => 'پیام اول تکراری',
            ])
            ->assertStatus(402)
            ->assertJson([
                'error' => 'PREMIUM_REQUIRED',
                'receiver_id' => $firstUser->id,
            ]);

        $this->assertSame(
            1,
            Message::query()
                ->whereIn('sender_id', [$firstUser->id, $secondUser->id])
                ->whereIn('receiver_id', [$firstUser->id, $secondUser->id])
                ->count()
        );
    }

    public function test_premium_participant_can_send_after_first_message(): void
    {
        $sender = User::factory()->create([
            'premium_until' => now()->addDay(),
        ]);
        $receiver = User::factory()->create();

        foreach (['پیام اول', 'پیام دوم'] as $message) {
            $this->actingAs($sender)
                ->postJson(route('messages.store'), [
                    'receiver_id' => $receiver->id,
                    'message' => $message,
                ])
                ->assertOk();
        }

        $this->assertSame(
            2,
            Message::query()
                ->where('sender_id', $sender->id)
                ->where('receiver_id', $receiver->id)
                ->count()
        );
    }
}
