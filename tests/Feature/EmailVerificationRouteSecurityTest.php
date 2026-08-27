<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class EmailVerificationRouteSecurityTest extends TestCase
{
    use DatabaseTransactions;

    public function test_unverified_user_is_redirected_from_sensitive_features(): void
    {
        $user = User::factory()
            ->unverified()
            ->create();

        $requests = [
            ['get', route('home')],
            ['get', route('search')],
            ['get', route('profile.photo', $user)],
            ['get', route('messages.index')],
            ['post', route('report.store')],
            ['post', route('premium.verifyCrypto')],
        ];

        foreach ($requests as [$method, $url]) {
            $this
                ->actingAs($user)
                ->{$method}($url)
                ->assertRedirect(route('verification.notice'));
        }
    }

    public function test_every_user_feature_route_has_verified_middleware(): void
    {
        $routeNames = [
            'home',
            'dashboard',
            'search',
            'profile.photo',
            'premium.upgrade',
            'premium.verifyCrypto',
            'profile.edit',
            'profile.update',
            'profile.show',
            'password.request',
            'password.email',
            'password.reset',
            'password.update',
            'messages.index',
            'messages.show',
            'messages.store',
            'likes.index',
            'likes.received',
            'like.store',
            'user.tickets.index',
            'user.tickets.create',
            'user.tickets.store',
            'police.index',
            'user.block',
            'user.unblock',
            'report.store',
        ];

        foreach ($routeNames as $routeName) {
            $route = Route::getRoutes()
                ->getByName($routeName);

            $this->assertNotNull(
                $route,
                "Route [{$routeName}] is missing."
            );

            $this->assertContains(
                'verified',
                $route->gatherMiddleware(),
                "Route [{$routeName}] does not require a verified email."
            );
        }
    }

    public function test_verification_and_logout_routes_remain_available(): void
    {
        $routeNames = [
            'logout',
            'verification.notice',
            'verification.verify',
            'verification.resend',
        ];

        foreach ($routeNames as $routeName) {
            $route = Route::getRoutes()
                ->getByName($routeName);

            $this->assertNotNull(
                $route,
                "Route [{$routeName}] is missing."
            );

            $this->assertNotContains(
                'verified',
                $route->gatherMiddleware(),
                "Route [{$routeName}] must remain accessible before verification."
            );
        }
    }
}
