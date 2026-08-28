<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class UserPresenceTest extends TestCase
{
    use DatabaseTransactions;

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_authenticated_web_request_records_activity(): void
    {
        Carbon::setTestNow('2026-08-28 10:00:00');

        $user = User::factory()->create([
            'last_seen_at' => null,
        ]);

        $profileUpdatedAt = $user->updated_at;

        $this->actingAs($user)
            ->get(route('home'))
            ->assertOk();

        $user->refresh();

        $this->assertTrue(
            $user->last_seen_at->equalTo(now())
        );

        $this->assertTrue($user->isOnline());

        $this->assertTrue(
            $user->updated_at->equalTo(
                $profileUpdatedAt
            )
        );
    }

    public function test_presence_write_is_limited_to_once_per_minute(): void
    {
        Carbon::setTestNow('2026-08-28 10:00:00');

        $previousLastSeen = now()->subSeconds(30);

        $user = User::factory()->create([
            'last_seen_at' => $previousLastSeen,
        ]);

        $this->actingAs($user)
            ->get(route('home'))
            ->assertOk();

        $this->assertTrue(
            $user->fresh()->last_seen_at->equalTo(
                $previousLastSeen
            )
        );
    }

    public function test_online_filter_uses_last_seen_at(): void
    {
        Carbon::setTestNow('2026-08-28 10:00:00');

        $viewer = User::factory()->create();

        $onlineUser = User::factory()->create([
            'last_seen_at' => now()->subMinutes(4),
        ]);

        User::factory()->create([
            'last_seen_at' => now()->subMinutes(6),
        ]);

        User::factory()->create([
            'last_seen_at' => null,
        ]);

        $this->actingAs($viewer)
            ->get(route('search', [
                'is_active' => 1,
            ]))
            ->assertOk()
            ->assertViewHas(
                'profiles',
                fn ($profiles): bool =>
                    $profiles->pluck('id')->all() ===
                    [$onlineUser->id]
            );
    }

    public function test_chat_status_uses_real_activity(): void
    {
        Carbon::setTestNow('2026-08-28 10:00:00');

        $viewer = User::factory()->create();

        $otherUser = User::factory()->create([
            'last_seen_at' => now()->subMinutes(4),
        ]);

        $this->actingAs($viewer)
            ->get(route('messages.show', $otherUser))
            ->assertOk()
            ->assertSee('آنلاین')
            ->assertDontSee('آفلاین');

        $otherUser->forceFill([
            'last_seen_at' => now()->subMinutes(6),
        ])->save();

        $this->actingAs($viewer)
            ->get(route('messages.show', $otherUser))
            ->assertOk()
            ->assertSee('آفلاین');
    }

    public function test_dashboard_lists_only_online_discoverable_members(): void
    {
        Carbon::setTestNow('2026-08-28 10:00:00');

        $viewer = User::factory()->create();

        $onlineUser = User::factory()->create([
            'last_seen_at' => now()->subMinutes(4),
        ]);

        User::factory()->create([
            'last_seen_at' => now()->subMinutes(6),
        ]);

        $this->actingAs($viewer)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertViewHas(
                'recentUsers',
                fn ($users): bool =>
                    $users->pluck('id')->all() ===
                    [$onlineUser->id]
            );
    }
}
