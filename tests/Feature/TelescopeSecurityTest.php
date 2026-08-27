<?php

namespace Tests\Feature;

use App\Http\Middleware\EnsureAdminTwoFactorVerified;
use App\Models\User;
use App\Providers\TelescopeServiceProvider;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Laravel\Telescope\Telescope;
use Tests\TestCase;

class TelescopeSecurityTest extends TestCase
{
    use DatabaseTransactions;

    public function test_telescope_is_disabled_by_default(): void
    {
        $this->assertFalse(
            (bool) config('telescope.enabled')
        );

        $this->assertFalse(
            Route::has('telescope')
        );

        $this->assertContains(
            EnsureAdminTwoFactorVerified::class,
            config('telescope.middleware')
        );

        $this->get('/telescope')->assertNotFound();
    }

    public function test_telescope_is_not_an_unconditional_provider(): void
    {
        $providers = require base_path(
            'bootstrap/providers.php'
        );

        $this->assertNotContains(
            TelescopeServiceProvider::class,
            $providers
        );
    }

    public function test_telescope_gate_allows_only_active_admins(): void
    {
        $this->app->register(
            TelescopeServiceProvider::class,
            true
        );

        $user = User::factory()->create();

        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $bannedAdmin = User::factory()->create([
            'role' => 'admin',
            'banned' => true,
        ]);

        $this->assertFalse(
            Gate::forUser($user)
                ->allows('viewTelescope')
        );

        $this->assertFalse(
            Gate::forUser($bannedAdmin)
                ->allows('viewTelescope')
        );

        $this->assertTrue(
            Gate::forUser($admin)
                ->allows('viewTelescope')
        );
    }

    public function test_local_environment_does_not_bypass_authorization(): void
    {
        $this->app['env'] = 'local';

        $this->app->register(
            TelescopeServiceProvider::class,
            true
        );

        $request = Request::create('/telescope');

        $this->assertFalse(
            Telescope::check($request)
        );
    }
}
