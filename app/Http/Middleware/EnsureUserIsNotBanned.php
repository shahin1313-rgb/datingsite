<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsNotBanned
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && (bool) $user->banned) {
            Auth::logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            $message = 'حساب کاربری شما توسط مدیریت مسدود شده است.';

            // برای درخواست‌های AJAX مانند ارسال پیام
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $message,
                ], 403);
            }

            return redirect()
                ->route('login')
                ->withErrors([
                    'email' => $message,
                ]);
        }

        return $next($request);
    }
}