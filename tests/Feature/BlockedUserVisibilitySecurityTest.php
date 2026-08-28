<?php

namespace Tests\Feature;

use App\Models\Block;
use App\Models\Like;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class BlockedUserVisibilitySecurityTest extends TestCase
{
    use DatabaseTransactions;

    public function test_home_and_search_only_show_discoverable_members(): void
    {
        $viewer = User::factory()->create();
        $visible = User::factory()->create();
        $blockedByViewer = User::factory()->create();
        $blockedViewer = User::factory()->create();

        $banned = User::factory()->create([
            'banned' => true,
        ]);

        $unverified = User::factory()
            ->unverified()
            ->create();

        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        Block::create([
            'blocker_id' => $viewer->id,
            'blocked_id' => $blockedByViewer->id,
        ]);

        Block::create([
            'blocker_id' => $blockedViewer->id,
            'blocked_id' => $viewer->id,
        ]);

        foreach (['home', 'search'] as $route) {
            $response = $this->actingAs($viewer)
                ->get(route($route));

            $response->assertOk();

            $this->assertProfileVisibility(
                $response,
                [$visible->id],
                [
                    $blockedByViewer->id,
                    $blockedViewer->id,
                    $banned->id,
                    $unverified->id,
                    $admin->id,
                ]
            );
        }
    }

    public function test_a_blocked_profile_is_hidden_in_both_directions(): void
    {
        $viewer = User::factory()->create();
        $blockedByViewer = User::factory()->create();
        $blockedViewer = User::factory()->create();

        Block::create([
            'blocker_id' => $viewer->id,
            'blocked_id' => $blockedByViewer->id,
        ]);

        Block::create([
            'blocker_id' => $blockedViewer->id,
            'blocked_id' => $viewer->id,
        ]);

        foreach ([$blockedByViewer, $blockedViewer] as $target) {
            $this->actingAs($viewer)
                ->get(route('profile.show', $target->id))
                ->assertNotFound();

            $this->assertDatabaseMissing('profile_views', [
                'viewer_id' => $viewer->id,
                'viewed_id' => $target->id,
            ]);
        }
    }

    public function test_ineligible_accounts_cannot_be_viewed_or_liked(): void
    {
        $viewer = User::factory()->create();

        $targets = [
            User::factory()->create([
                'banned' => true,
            ]),
            User::factory()->unverified()->create(),
            User::factory()->create([
                'role' => 'admin',
            ]),
        ];

        foreach ($targets as $target) {
            $this->actingAs($viewer)
                ->get(route('profile.show', $target->id))
                ->assertNotFound();

            $this->actingAs($viewer)
                ->post(route('like.store', $target->id))
                ->assertNotFound();

            $this->assertDatabaseMissing('likes', [
                'user_id' => $viewer->id,
                'liked_user_id' => $target->id,
            ]);
        }
    }

    public function test_like_is_rejected_in_both_block_directions(): void
    {
        $viewer = User::factory()->create();
        $blockedByViewer = User::factory()->create();
        $blockedViewer = User::factory()->create();

        Block::create([
            'blocker_id' => $viewer->id,
            'blocked_id' => $blockedByViewer->id,
        ]);

        Block::create([
            'blocker_id' => $blockedViewer->id,
            'blocked_id' => $viewer->id,
        ]);

        foreach ([$blockedByViewer, $blockedViewer] as $target) {
            $this->actingAs($viewer)
                ->post(route('like.store', $target->id))
                ->assertNotFound();

            $this->assertDatabaseMissing('likes', [
                'user_id' => $viewer->id,
                'liked_user_id' => $target->id,
            ]);
        }
    }

    public function test_discoverable_member_can_still_be_liked(): void
    {
        $viewer = User::factory()->create();
        $target = User::factory()->create();

        $this->actingAs($viewer)
            ->post(route('like.store', $target->id))
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('likes', [
            'user_id' => $viewer->id,
            'liked_user_id' => $target->id,
        ]);
    }

    public function test_likes_page_hides_blocked_and_ineligible_accounts(): void
    {
        $viewer = User::factory()->create();
        $visibleSent = User::factory()->create();
        $visibleReceived = User::factory()->create();
        $blocked = User::factory()->create();

        $banned = User::factory()->create([
            'banned' => true,
        ]);

        $unverified = User::factory()
            ->unverified()
            ->create();

        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        Block::create([
            'blocker_id' => $blocked->id,
            'blocked_id' => $viewer->id,
        ]);

        foreach (
            [$visibleSent, $blocked, $banned, $unverified, $admin]
            as $target
        ) {
            Like::create([
                'user_id' => $viewer->id,
                'liked_user_id' => $target->id,
            ]);
        }

        foreach (
            [$visibleReceived, $blocked, $banned, $unverified, $admin]
            as $liker
        ) {
            Like::create([
                'user_id' => $liker->id,
                'liked_user_id' => $viewer->id,
            ]);
        }

        $this->actingAs($viewer)
            ->get(route('likes.index'))
            ->assertOk()
            ->assertViewHas(
                'likedUsers',
                fn ($users): bool =>
                    $users->pluck('id')->all() ===
                    [$visibleSent->id]
            )
            ->assertViewHas(
                'likedByUsers',
                fn ($users): bool =>
                    $users->pluck('id')->all() ===
                    [$visibleReceived->id]
            );
    }

    public function test_blocking_removes_existing_likes_in_both_directions(): void
    {
        $viewer = User::factory()->create();
        $target = User::factory()->create();

        Like::create([
            'user_id' => $viewer->id,
            'liked_user_id' => $target->id,
        ]);

        Like::create([
            'user_id' => $target->id,
            'liked_user_id' => $viewer->id,
        ]);

        $this->actingAs($viewer)
            ->post(route('user.block', $target->id))
            ->assertRedirect();

        $this->assertDatabaseHas('blocks', [
            'blocker_id' => $viewer->id,
            'blocked_id' => $target->id,
        ]);

        $this->assertDatabaseMissing('likes', [
            'user_id' => $viewer->id,
            'liked_user_id' => $target->id,
        ]);

        $this->assertDatabaseMissing('likes', [
            'user_id' => $target->id,
            'liked_user_id' => $viewer->id,
        ]);
    }

    /**
     * @param array<int, int> $expected
     */
    private function assertProfileVisibility(
        TestResponse $response,
        array $visible,
        array $hidden
    ): void {
        $response->assertViewHas(
            'profiles',
            function ($profiles) use ($visible, $hidden): bool {
                $actual = $profiles
                    ->getCollection()
                    ->pluck('id')
                    ->all();

                return count(array_diff($visible, $actual)) === 0
                    && count(array_intersect($hidden, $actual)) === 0;
            }
        );
    }
}
