<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Laravel\Telescope\IncomingEntry;
use Laravel\Telescope\Telescope;
use Laravel\Telescope\TelescopeApplicationServiceProvider;

class TelescopeServiceProvider extends TelescopeApplicationServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Telescope::night();

        $this->hideSensitiveRequestDetails();

        $recordAllEntries = $this->app->environment('local');

        Telescope::filter(
            function (IncomingEntry $entry) use ($recordAllEntries) {
                return $recordAllEntries ||
                       $entry->isReportableException() ||
                       $entry->isFailedRequest() ||
                       $entry->isFailedJob() ||
                       $entry->isScheduledTask() ||
                       $entry->hasMonitoredTag();
            }
        );
    }

    /**
     * Prevent sensitive request details from being logged by Telescope.
     */
    protected function hideSensitiveRequestDetails(): void
    {
        Telescope::hideRequestParameters([
            '_token',
            'password',
            'password_confirmation',
            'current_password',
            'token',
            'code',
        ]);

        Telescope::hideRequestHeaders([
            'authorization',
            'cookie',
            'x-csrf-token',
            'x-xsrf-token',
        ]);

        Telescope::hideResponseParameters([
            'token',
            'access_token',
            'refresh_token',
        ]);
    }

    /**
     * Require authorization in every environment, including local.
     */
    protected function authorization(): void
    {
        $this->gate();

        Telescope::auth(
            static function (Request $request): bool {
                return Gate::check(
                    'viewTelescope',
                    [$request->user()]
                );
            }
        );
    }

    /**
     * Register the Telescope gate.
     *
     * This gate determines who can access Telescope in every environment.
     */
    protected function gate(): void
    {
        Gate::define(
            'viewTelescope',
            static function (?User $user): bool {
                return $user?->isAdmin() === true &&
                    ! (bool) $user->banned;
            }
        );
    }
}
