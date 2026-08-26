<?php

use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\AddSecurityHeaders;
use App\Http\Middleware\EnsureAdminTwoFactorVerified;
use App\Http\Middleware\EnsureUserIsNotBanned;
use App\Http\Middleware\SetLocale;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(
    basePath: dirname(__DIR__)
)
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(
        function (Middleware $middleware): void {
            $middleware->alias([
                'admin' => AdminMiddleware::class,

                'auth' => Authenticate::class,

                'not_banned' => EnsureUserIsNotBanned::class,

                'admin.2fa' => EnsureAdminTwoFactorVerified::class,
            ]);

            $middleware->redirectGuestsTo(
                function (Request $request): string {
                    if (
                        $request->is('admin') ||
                        $request->is('admin/*')
                    ) {
                        return route('admin.login');
                    }

                    return route('login');
                }
            );

            $middleware->web(
                append: [
                    SetLocale::class,
                    AddSecurityHeaders::class,
                ]
            );
        }
    )
    ->withExceptions(
        function (Exceptions $exceptions): void {
            //
        }
    )
    ->create();