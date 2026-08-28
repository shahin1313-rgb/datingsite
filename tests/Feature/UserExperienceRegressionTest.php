<?php

namespace Tests\Feature;

use App\Models\Message;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class UserExperienceRegressionTest extends TestCase
{
    use DatabaseTransactions;

    public function test_marital_status_is_saved_from_profile_edit_form(): void
    {
        $user = User::factory()->create([
            'marital_status' => 'single',
        ]);

        $response = $this->actingAs($user)
            ->post(route('profile.update'), [
                'name' => $user->name,
                'email' => $user->email,
                'city' => $user->city,
                'bio' => $user->bio,
                'marital_status' => 'married',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('dashboard'));

        $this->assertSame(
            'married',
            $user->fresh()->marital_status
        );
    }

    public function test_invalid_marital_status_is_rejected(): void
    {
        $user = User::factory()->create([
            'marital_status' => 'single',
        ]);

        $response = $this->actingAs($user)
            ->post(route('profile.update'), [
                'name' => $user->name,
                'email' => $user->email,
                'city' => $user->city,
                'bio' => $user->bio,
                'marital_status' => 'unsupported-value',
            ]);

        $response->assertSessionHasErrors('marital_status');

        $this->assertSame(
            'single',
            $user->fresh()->marital_status
        );
    }

    public function test_like_endpoint_toggles_the_persisted_like(): void
    {
        $user = User::factory()->create();
        $target = User::factory()->create();

        $this->actingAs($user)
            ->postJson(route('like.store', $target->id))
            ->assertOk()
            ->assertJson([
                'liked' => true,
                'matched' => false,
            ]);

        $this->assertDatabaseHas('likes', [
            'user_id' => $user->id,
            'liked_user_id' => $target->id,
        ]);

        $this->actingAs($user)
            ->postJson(route('like.store', $target->id))
            ->assertOk()
            ->assertJson([
                'liked' => false,
                'matched' => false,
            ]);

        $this->assertDatabaseMissing('likes', [
            'user_id' => $user->id,
            'liked_user_id' => $target->id,
        ]);
    }

    public function test_admin_ticket_reply_is_visible_only_to_ticket_owner(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $ticket = Ticket::create([
            'user_id' => $owner->id,
            'subject' => 'OWNER-SUPPORT-TICKET',
            'message' => 'OWNER-SUPPORT-MESSAGE',
        ]);

        Ticket::create([
            'user_id' => $admin->id,
            'parent_id' => $ticket->id,
            'subject' => 'پاسخ پشتیبانی',
            'message' => 'VISIBLE-ADMIN-REPLY',
        ]);

        $this->actingAs($owner)
            ->get(route('user.tickets.index'))
            ->assertOk()
            ->assertSee('VISIBLE-ADMIN-REPLY')
            ->assertSee('مدیریت پشتیبانی');

        $this->actingAs($otherUser)
            ->get(route('user.tickets.index'))
            ->assertOk()
            ->assertDontSee('VISIBLE-ADMIN-REPLY');
    }

    public function test_dashboard_shows_real_message_and_membership_counts(): void
    {
        $user = User::factory()->create([
            'premium_until' => now()->addDays(7),
        ]);
        $contact = User::factory()->create();
        $unrelatedUser = User::factory()->create();

        Message::create([
            'sender_id' => $user->id,
            'receiver_id' => $contact->id,
            'message' => 'SENT-MESSAGE',
            'status' => 'sent',
        ]);

        Message::create([
            'sender_id' => $contact->id,
            'receiver_id' => $user->id,
            'message' => 'RECEIVED-MESSAGE',
            'status' => 'sent',
        ]);

        Message::create([
            'sender_id' => $contact->id,
            'receiver_id' => $unrelatedUser->id,
            'message' => 'UNRELATED-MESSAGE',
            'status' => 'sent',
        ]);

        $response = $this->actingAs($user)
            ->get(route('dashboard'));

        $response
            ->assertOk()
            ->assertViewHas('messagesCount', 2)
            ->assertViewHas('membershipDaysRemaining', 7);
    }
}
