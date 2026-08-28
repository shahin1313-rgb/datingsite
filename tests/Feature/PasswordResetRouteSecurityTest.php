<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class PasswordResetRouteSecurityTest extends TestCase
{
    use DatabaseTransactions;

    public function test_password_reset_routes_are_guest_only(): void
    {
        $routeNames = [
            'password.request',
            'password.email',
            'password.reset',
            'password.update',
        ];

        foreach ($routeNames as $routeName) {
            $route = Route::getRoutes()
                ->getByName($routeName);

            $this->assertNotNull(
                $route,
                "Route [{$routeName}] is missing."
            );

            $middleware = $route->gatherMiddleware();

            $this->assertContains(
                'guest',
                $middleware,
                "Route [{$routeName}] must be guest-only."
            );

            foreach (['auth', 'verified', 'not_banned'] as $blockedMiddleware) {
                $this->assertNotContains(
                    $blockedMiddleware,
                    $middleware,
                    "Route [{$routeName}] must not use [{$blockedMiddleware}]."
                );
            }
        }
    }

    public function test_guest_can_request_a_password_reset_link(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $this->post(route('password.email'), [
            'email' => $user->email,
        ])->assertRedirect();

        Notification::assertSentTo(
            $user,
            ResetPassword::class
        );
    }

    public function test_password_reset_email_requests_are_rate_limited(): void
    {
        for ($attempt = 1; $attempt <= 5; $attempt++) {
            $this->post(route('password.email'), [
                'email' => "missing{$attempt}@example.test",
            ])->assertRedirect();
        }

        $this->post(route('password.email'), [
            'email' => 'blocked@example.test',
        ])->assertStatus(429);
    }

    public function test_authenticated_user_is_redirected_from_password_reset_routes(): void
    {
        $user = User::factory()->create();

        $this
            ->actingAs($user)
            ->get(route('password.request'))
            ->assertRedirect(route('dashboard'));

        $this
            ->actingAs($user)
            ->get(route('password.reset', 'test-token'))
            ->assertRedirect(route('dashboard'));
    }
}
