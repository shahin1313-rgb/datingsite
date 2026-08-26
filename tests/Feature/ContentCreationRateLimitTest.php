<?php

namespace Tests\Feature;

use App\Models\Message;
use App\Models\Report;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ContentCreationRateLimitTest extends TestCase
{
    use DatabaseTransactions;

    public function test_message_creation_is_limited_to_ten_per_minute(): void
    {
        $sender = User::factory()->create([
            'premium_until' => now()->addDay(),
        ]);
        $receiver = User::factory()->create();

        foreach (range(1, 10) as $attempt) {
            $this->actingAs($sender)
                ->postJson(route('messages.store'), [
                    'receiver_id' => $receiver->id,
                    'message' => 'پیام آزمایشی '.$attempt,
                ])
                ->assertOk();
        }

        $this->actingAs($sender)
            ->postJson(route('messages.store'), [
                'receiver_id' => $receiver->id,
                'message' => 'این پیام نباید ذخیره شود',
            ])
            ->assertStatus(429);

        $this->assertSame(
            10,
            Message::query()
                ->where('sender_id', $sender->id)
                ->count()
        );
    }

    public function test_ticket_creation_is_limited_to_three_per_hour(): void
    {
        $user = User::factory()->create();

        foreach (range(1, 3) as $attempt) {
            $this->actingAs($user)
                ->post(route('user.tickets.store'), [
                    'subject' => 'درخواست پشتیبانی '.$attempt,
                    'message' => 'متن تیکت آزمایشی',
                ])
                ->assertRedirect();
        }

        $this->actingAs($user)
            ->post(route('user.tickets.store'), [
                'subject' => 'تیکت اضافی',
                'message' => 'این تیکت نباید ذخیره شود',
            ])
            ->assertStatus(429);

        $this->assertSame(
            3,
            Ticket::query()
                ->where('user_id', $user->id)
                ->count()
        );
    }

    public function test_report_creation_is_limited_per_authenticated_user(): void
    {
        $reporter = User::factory()->create();
        $otherReporter = User::factory()->create();
        $reported = User::factory()->create();

        foreach (range(1, 5) as $attempt) {
            $this->actingAs($reporter)
                ->post(route('report.store'), [
                    'reported_id' => $reported->id,
                    'reason' => 'گزارش آزمایشی '.$attempt,
                ])
                ->assertRedirect();
        }

        $this->actingAs($reporter)
            ->post(route('report.store'), [
                'reported_id' => $reported->id,
                'reason' => 'این گزارش نباید ذخیره شود',
            ])
            ->assertStatus(429);

        $this->actingAs($otherReporter)
            ->post(route('report.store'), [
                'reported_id' => $reported->id,
                'reason' => 'گزارش کاربر دیگر',
            ])
            ->assertRedirect();

        $this->assertSame(
            5,
            Report::query()
                ->where('reporter_id', $reporter->id)
                ->count()
        );
        $this->assertSame(
            1,
            Report::query()
                ->where('reporter_id', $otherReporter->id)
                ->count()
        );
    }

    public function test_ticket_message_cannot_exceed_five_thousand_characters(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('user.tickets.store'), [
                'subject' => 'متن بیش از حد مجاز',
                'message' => str_repeat('a', 5001),
            ])
            ->assertSessionHasErrors('message');

        $this->assertFalse(
            Ticket::query()
                ->where('user_id', $user->id)
                ->exists()
        );
    }
}
