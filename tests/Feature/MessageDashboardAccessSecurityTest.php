<?php

namespace Tests\Feature;

use App\Models\Block;
use App\Models\Like;
use App\Models\Message;
use App\Models\ProfileView;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class MessageDashboardAccessSecurityTest extends TestCase
{
    use DatabaseTransactions;

    public function test_message_show_and_store_reject_idor_targets(): void
    {
        $sender = User::factory()->create();
        $blockedBySender = User::factory()->create();
        $blockedSender = User::factory()->create();

        $targets = [
            User::factory()->create(['role' => 'admin']),
            User::factory()->create(['banned' => true]),
            User::factory()->unverified()->create(),
            $blockedBySender,
            $blockedSender,
        ];

        Block::create([
            'blocker_id' => $sender->id,
            'blocked_id' => $blockedBySender->id,
        ]);

        Block::create([
            'blocker_id' => $blockedSender->id,
            'blocked_id' => $sender->id,
        ]);

        foreach ($targets as $target) {
            $this->actingAs($sender)
                ->get(route('messages.show', $target))
                ->assertNotFound();

            $this->actingAs($sender)
                ->postJson(route('messages.store'), [
                    'receiver_id' => $target->id,
                    'message' => 'این پیام نباید ذخیره شود.',
                ])
                ->assertNotFound();

            $this->assertDatabaseMissing('messages', [
                'sender_id' => $sender->id,
                'receiver_id' => $target->id,
            ]);
        }
    }

    public function test_discoverable_member_remains_a_valid_message_recipient(): void
    {
        $sender = User::factory()->create();
        $receiver = User::factory()->create();

        $this->actingAs($sender)
            ->get(route('messages.show', $receiver))
            ->assertOk()
            ->assertViewHas(
                'user',
                fn (User $user): bool => $user->is($receiver)
            );

        $this->actingAs($sender)
            ->postJson(route('messages.store'), [
                'receiver_id' => $receiver->id,
                'message' => 'پیام مجاز',
            ])
            ->assertOk()
            ->assertJson([
                'success' => true,
                'type' => 'FIRST_MESSAGE_PRIVATE',
            ]);

        $this->assertDatabaseHas('messages', [
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
            'message' => 'پیام مجاز',
        ]);
    }

    public function test_message_index_hides_ineligible_and_blocked_contacts(): void
    {
        $viewer = User::factory()->create();
        $visible = User::factory()->create();
        $blocked = User::factory()->create();
        $admin = User::factory()->create(['role' => 'admin']);
        $banned = User::factory()->create(['banned' => true]);
        $unverified = User::factory()->unverified()->create();

        Block::create([
            'blocker_id' => $blocked->id,
            'blocked_id' => $viewer->id,
        ]);

        foreach (
            [$visible, $blocked, $admin, $banned, $unverified]
            as $contact
        ) {
            Message::create([
                'sender_id' => $contact->id,
                'receiver_id' => $viewer->id,
                'message' => 'گفتگوی قدیمی',
                'status' => 'sent',
            ]);
        }

        $this->actingAs($viewer)
            ->get(route('messages.index'))
            ->assertOk()
            ->assertViewHas(
                'contacts',
                function ($contacts) use ($visible): bool {
                    $ids = array_map(
                        'intval',
                        $contacts->keys()->all()
                    );

                    return $ids === [$visible->id];
                }
            );
    }

    public function test_dashboard_relations_and_counts_only_include_discoverable_members(): void
    {
        $viewer = User::factory()->create();
        $visible = User::factory()->create([
            'last_login_at' => now()->subMinutes(5),
        ]);
        $blockedByViewer = User::factory()->create([
            'last_login_at' => now()->subMinutes(4),
        ]);
        $blockedViewer = User::factory()->create([
            'last_login_at' => now()->subMinutes(3),
        ]);
        $admin = User::factory()->create([
            'role' => 'admin',
            'last_login_at' => now()->subMinutes(2),
        ]);
        $banned = User::factory()->create([
            'banned' => true,
            'last_login_at' => now()->subMinute(),
        ]);
        $unverified = User::factory()->unverified()->create([
            'last_login_at' => now(),
        ]);

        Block::create([
            'blocker_id' => $viewer->id,
            'blocked_id' => $blockedByViewer->id,
        ]);

        Block::create([
            'blocker_id' => $blockedViewer->id,
            'blocked_id' => $viewer->id,
        ]);

        foreach (
            [
                $visible,
                $blockedByViewer,
                $blockedViewer,
                $admin,
                $banned,
                $unverified,
            ]
            as $member
        ) {
            ProfileView::create([
                'viewer_id' => $member->id,
                'viewed_id' => $viewer->id,
            ]);

            Like::create([
                'user_id' => $member->id,
                'liked_user_id' => $viewer->id,
            ]);
        }

        $response = $this->actingAs($viewer)
            ->get(route('dashboard'));

        $response->assertOk();
        $response->assertViewHas('totalViews', 1);
        $response->assertViewHas('todayViews', 1);
        $response->assertViewHas('likesCount', 1);
        $response->assertViewHas('todayLikes', 1);
        $this->assertUserIds($response, 'recentUsers', [$visible->id]);
        $this->assertRelatedUserIds(
            $response,
            'latestViewers',
            'viewer_id',
            [$visible->id]
        );
        $this->assertRelatedUserIds(
            $response,
            'recentProfileViews',
            'viewer_id',
            [$visible->id]
        );
        $this->assertRelatedUserIds(
            $response,
            'latestLikers',
            'user_id',
            [$visible->id]
        );
    }

    /**
     * @param array<int, int> $expected
     */
    private function assertUserIds(
        TestResponse $response,
        string $key,
        array $expected
    ): void {
        $response->assertViewHas(
            $key,
            fn ($users): bool =>
                $users->pluck('id')->all() === $expected
        );
    }

    /**
     * @param array<int, int> $expected
     */
    private function assertRelatedUserIds(
        TestResponse $response,
        string $key,
        string $column,
        array $expected
    ): void {
        $response->assertViewHas(
            $key,
            fn ($records): bool =>
                $records->pluck($column)->all() === $expected
        );
    }
}
