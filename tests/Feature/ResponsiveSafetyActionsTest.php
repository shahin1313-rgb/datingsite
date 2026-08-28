<?php

namespace Tests\Feature;

use App\Models\Message;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ResponsiveSafetyActionsTest extends TestCase
{
    use DatabaseTransactions;

    public function test_authenticated_layout_uses_a_desktop_width_container(): void
    {
        $viewer = User::factory()->create();

        $this->actingAs($viewer)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('data-app-content-container', false)
            ->assertSee('class="w-full max-w-7xl mx-auto"', false);
    }

    public function test_profile_exposes_block_and_report_controls(): void
    {
        $viewer = User::factory()->create();
        $target = User::factory()->create();

        $this->actingAs($viewer)
            ->get(route('profile.show', $target->id))
            ->assertOk()
            ->assertSee('data-profile-block', false)
            ->assertSee('data-report-open', false)
            ->assertSee(route('user.block', $target->id), false)
            ->assertSee(route('report.store'), false);
    }

    public function test_chat_exposes_touch_accessible_safety_actions(): void
    {
        $viewer = User::factory()->create();
        $target = User::factory()->create();

        $this->actingAs($viewer)
            ->get(route('messages.show', $target))
            ->assertOk()
            ->assertSee('id="chatSafetyMenuButton"', false)
            ->assertSee('data-chat-block', false)
            ->assertSee('data-chat-report-open', false)
            ->assertSee(route('user.block', $target->id), false)
            ->assertSee(route('report.store'), false);
    }

    public function test_message_list_block_action_is_not_hover_only(): void
    {
        $viewer = User::factory()->create();
        $target = User::factory()->create();

        Message::create([
            'sender_id' => $target->id,
            'receiver_id' => $viewer->id,
            'message' => 'پیام آزمایشی',
            'status' => 'sent',
        ]);

        $this->actingAs($viewer)
            ->get(route('messages.index'))
            ->assertOk()
            ->assertSee('data-mobile-block-action', false)
            ->assertDontSee(
                'opacity-0 group-hover:opacity-100',
                false
            );
    }

    public function test_block_redirects_to_an_accessible_page(): void
    {
        $viewer = User::factory()->create();
        $target = User::factory()->create();

        $this->actingAs($viewer)
            ->post(route('user.block', $target->id))
            ->assertRedirect(route('messages.index'));

        $this->assertDatabaseHas('blocks', [
            'blocker_id' => $viewer->id,
            'blocked_id' => $target->id,
        ]);
    }
}
