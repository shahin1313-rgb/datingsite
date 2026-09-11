<?php

namespace Tests\Feature;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class V15SecurityAndUxHardeningTest extends TestCase
{
    use DatabaseTransactions;

    public function test_password_reset_request_does_not_reveal_account_existence(): void
    {
        Notification::fake();
        $user = User::factory()->create();
        $generic = 'اگر حسابی با این ایمیل وجود داشته باشد، لینک بازیابی برای آن ارسال می‌شود.';

        $this->post(route('password.email'), ['email' => $user->email])
            ->assertSessionHas('status', $generic);
        $this->post(route('password.email'), ['email' => 'missing@example.test'])
            ->assertSessionHas('status', $generic);
    }

    public function test_private_admin_messages_require_reason_password_and_are_audited(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'password' => Hash::make('password'),
        ]);

        $this->asVerifiedAdmin($admin)
            ->get(route('admin.messages'))
            ->assertOk()
            ->assertSee('دسترسی محافظت‌شده');

        $this->asVerifiedAdmin($admin)
            ->post(route('admin.messages.access'), [
                'reason' => 'رسیدگی به گزارش سوءاستفاده ثبت‌شده',
                'current_password' => 'password',
            ])
            ->assertRedirect(route('admin.messages'));

        $this->assertDatabaseHas('admin_audit_logs', [
            'actor_id' => $admin->id,
            'action' => 'private_messages.access_granted',
        ]);
    }

    public function test_admin_ticket_pages_render_and_closed_ticket_rejects_reply(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $owner = User::factory()->create();
        $ticket = Ticket::query()->create([
            'user_id' => $owner->id,
            'subject' => 'Help',
            'message' => 'Please help',
            'status' => 'closed',
        ]);

        $this->asVerifiedAdmin($admin)->get(route('admin.tickets.index'))->assertOk();
        $this->asVerifiedAdmin($admin)->get(route('admin.tickets.show', $ticket))->assertOk();
        $this->asVerifiedAdmin($admin)
            ->post(route('admin.tickets.reply', $ticket), ['message' => 'Reply'])
            ->assertSessionHasErrors('message');
    }

    private function asVerifiedAdmin(User $admin): static
    {
        return $this->actingAs($admin)->withSession([
            'admin_2fa_verified_at' => now()->timestamp,
        ]);
    }
}
