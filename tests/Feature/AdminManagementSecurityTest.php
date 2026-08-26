<?php

namespace Tests\Feature;

use App\Models\AdminAuditLog;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminManagementSecurityTest extends TestCase
{
    use DatabaseTransactions;

    private const PASSWORD = 'Secure-password-123';

    public function test_admin_cannot_ban_their_own_account(): void
    {
        $initialAuditCount =
            AdminAuditLog::query()->count();

        $admin = $this->makeAdmin();

        $response = $this
            ->asVerifiedAdmin($admin)
            ->post(
                route('admin.users.ban', $admin),
                [
                    'current_password' =>
                        self::PASSWORD,
                ]
            );

        $response->assertSessionHasErrors(
            'admin_action'
        );

        $this->assertFalse(
            (bool) $admin->fresh()->banned
        );

        /*
         * عملیات ردشده نباید رکورد جدیدی ایجاد کند.
         * رکوردهای قبلی جدول دست‌نخورده باقی می‌مانند.
         */
        $this->assertDatabaseCount(
            'admin_audit_logs',
            $initialAuditCount
        );
    }

    public function test_admin_cannot_delete_their_own_account(): void
    {
        $initialAuditCount =
            AdminAuditLog::query()->count();

        $admin = $this->makeAdmin();

        $response = $this
            ->asVerifiedAdmin($admin)
            ->delete(
                route('admin.users.destroy', $admin),
                [
                    'current_password' =>
                        self::PASSWORD,
                ]
            );

        $response->assertSessionHasErrors(
            'admin_action'
        );

        $this->assertDatabaseHas('users', [
            'id' => $admin->id,
        ]);

        /*
         * درخواست نامعتبر نباید Audit Log جدید بسازد.
         */
        $this->assertDatabaseCount(
            'admin_audit_logs',
            $initialAuditCount
        );
    }

    public function test_wrong_password_blocks_sensitive_action(): void
    {
        $initialAuditCount =
            AdminAuditLog::query()->count();

        $admin = $this->makeAdmin();
        $target = $this->makeUser();

        $response = $this
            ->asVerifiedAdmin($admin)
            ->post(
                route('admin.users.ban', $target),
                [
                    'current_password' =>
                        'wrong-password',
                ]
            );

        $response->assertSessionHasErrors(
            'current_password'
        );

        $this->assertFalse(
            (bool) $target->fresh()->banned
        );

        /*
         * رمز اشتباه نباید رکورد Audit جدید ایجاد کند.
         */
        $this->assertDatabaseCount(
            'admin_audit_logs',
            $initialAuditCount
        );
    }

    public function test_successful_ban_is_audited(): void
    {
        $admin = $this->makeAdmin();
        $target = $this->makeUser();

        $response = $this
            ->asVerifiedAdmin($admin)
            ->post(
                route('admin.users.ban', $target),
                [
                    'current_password' =>
                        self::PASSWORD,
                ]
            );

        $response->assertRedirect(
            route('admin.users')
        );

        $this->assertTrue(
            (bool) $target->fresh()->banned
        );

        $this->assertDatabaseHas(
            'admin_audit_logs',
            [
                'actor_id' => $admin->id,
                'target_user_id' => $target->id,
                'action' => 'user.banned',
            ]
        );
    }

    public function test_admin_promotion_is_audited(): void
    {
        $admin = $this->makeAdmin();
        $target = $this->makeUser();

        $response = $this
            ->asVerifiedAdmin($admin)
            ->patch(
                route('admin.makeAdmin', $target),
                [
                    'current_password' =>
                        self::PASSWORD,
                ]
            );

        $response->assertRedirect(
            route('admin.users')
        );

        $this->assertSame(
            'admin',
            $target->fresh()->role
        );

        $this->assertDatabaseHas(
            'admin_audit_logs',
            [
                'actor_id' => $admin->id,
                'target_user_id' => $target->id,
                'action' =>
                    'user.promoted_to_admin',
            ]
        );
    }

    public function test_admin_area_rejects_session_without_two_factor(): void
    {
        $admin = $this->makeAdmin();

        $response = $this
            ->actingAs($admin)
            ->get(
                route('admin.dashboard')
            );

        $response->assertRedirect(
            route('admin.login')
        );

        $this->assertGuest();
    }

    private function asVerifiedAdmin(
        User $admin
    ): static {
        return $this
            ->actingAs($admin)
            ->withSession([
                'admin_2fa_verified_at' =>
                    now()->timestamp,
            ]);
    }

    private function makeAdmin(): User
    {
        return User::factory()->create([
            'gender' => 'other',
            'role' => 'admin',
            'banned' => false,
            'password' => Hash::make(
                self::PASSWORD
            ),
        ]);
    }

    private function makeUser(): User
    {
        return User::factory()->create([
            'gender' => 'other',
            'role' => 'user',
            'banned' => false,
        ]);
    }
}