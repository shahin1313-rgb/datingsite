<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\AdminTwoFactorCode;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AdminTwoFactorAuthenticationTest extends TestCase
{
    use DatabaseTransactions;

    private const PASSWORD = 'Secure-password-123';

    public function test_valid_password_starts_two_factor_challenge(): void
    {
        Notification::fake();

        $admin = $this->makeAdmin();

        $response = $this->post(
            route('admin.login.submit'),
            [
                'email' => $admin->email,
                'password' => self::PASSWORD,
            ]
        );

        $response
            ->assertRedirect(
                route('admin.two-factor.form')
            )
            ->assertSessionHas(
                'admin_2fa_user_id',
                $admin->id
            );

        $this->assertGuest();

        Notification::assertSentTo(
            $admin,
            AdminTwoFactorCode::class
        );

        $this->assertNotNull(
            $admin
                ->fresh()
                ->admin_two_factor_code_hash
        );
    }

    public function test_correct_code_completes_admin_login(): void
    {
        Notification::fake();

        $admin = $this->makeAdmin();

        $code = null;

        $this->post(
            route('admin.login.submit'),
            [
                'email' => $admin->email,
                'password' => self::PASSWORD,
            ]
        );

        Notification::assertSentTo(
            $admin,
            AdminTwoFactorCode::class,
            function (
                AdminTwoFactorCode $notification
            ) use (&$code): bool {
                $code = $notification->code;

                return true;
            }
        );

        $response = $this
            ->withSession([
                'admin_2fa_user_id' =>
                    $admin->id,

                'admin_2fa_remember' =>
                    false,
            ])
            ->post(
                route('admin.two-factor.verify'),
                [
                    'code' => $code,
                ]
            );

        $response->assertRedirect(
            route('admin.dashboard')
        );

        $this->assertAuthenticatedAs($admin);

        $this->assertDatabaseHas(
            'admin_audit_logs',
            [
                'actor_id' => $admin->id,
                'action' =>
                    'admin.login.two_factor_verified',
            ]
        );

        $this->assertNull(
            $admin
                ->fresh()
                ->admin_two_factor_code_hash
        );
    }

    public function test_wrong_code_does_not_authenticate_admin(): void
    {
        $admin = $this->makeAdmin();

        $admin->forceFill([
            'admin_two_factor_code_hash' =>
                Hash::make('123456'),

            'admin_two_factor_expires_at' =>
                now()->addMinutes(5),
        ])->save();

        $response = $this
            ->withSession([
                'admin_2fa_user_id' =>
                    $admin->id,
            ])
            ->post(
                route('admin.two-factor.verify'),
                [
                    'code' => '654321',
                ]
            );

        $response->assertSessionHasErrors(
            'code'
        );

        $this->assertGuest();
    }

    public function test_expired_code_does_not_authenticate_admin(): void
    {
        $admin = $this->makeAdmin();

        $admin->forceFill([
            'admin_two_factor_code_hash' =>
                Hash::make('123456'),

            'admin_two_factor_expires_at' =>
                now()->subMinute(),
        ])->save();

        $response = $this
            ->withSession([
                'admin_2fa_user_id' =>
                    $admin->id,
            ])
            ->post(
                route('admin.two-factor.verify'),
                [
                    'code' => '123456',
                ]
            );

        $response->assertSessionHasErrors(
            'code'
        );

        $this->assertGuest();
    }

    public function test_normal_user_cannot_start_admin_challenge(): void
    {
        Notification::fake();

        $user = User::factory()->create([
            'gender' => 'other',
            'role' => 'user',
            'banned' => false,
            'password' => Hash::make(
                self::PASSWORD
            ),
        ]);

        $response = $this->post(
            route('admin.login.submit'),
            [
                'email' => $user->email,
                'password' => self::PASSWORD,
            ]
        );

        $response->assertSessionHasErrors(
            'email'
        );

        $this->assertGuest();

        Notification::assertNothingSent();
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
}