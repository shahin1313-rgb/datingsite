<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminTwoFactorVerified
{
    public function handle(
        Request $request,
        Closure $next
    ): Response|RedirectResponse {
        $verifiedAt = (int) $request
            ->session()
            ->get(
                'admin_2fa_verified_at',
                0
            );

        $timeout = (int) config(
            'auth.admin_two_factor_timeout',
            28800
        );

        if (
            $verifiedAt > 0 &&
            (time() - $verifiedAt) <= $timeout
        ) {
            return $next($request);
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('admin.login')
            ->withErrors([
                'email' =>
                    'برای ورود به مدیریت، تأیید دومرحله‌ای را تکمیل کنید.',
            ]);
    }
}