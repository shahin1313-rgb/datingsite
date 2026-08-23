<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AdminLoginController extends Controller
{
    /**
     * نمایش صفحه ورود مدیریت
     */
    public function showLoginForm(Request $request): View|RedirectResponse
    {
        if ($request->user()) {
            return $this->redirectAuthenticatedUser($request);
        }

        return view('admin.auth.login');
    }

    /**
     * ورود مدیر
     */
    public function login(Request $request): RedirectResponse
    {
        if ($request->user()) {
            return $this->redirectAuthenticatedUser($request);
        }

        $validated = $request->validate([
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
            ],
            'password' => [
                'required',
                'string',
                'max:255',
            ],
            'remember' => [
                'nullable',
                'boolean',
            ],
        ], [
            'email.required' => 'واردکردن ایمیل الزامی است.',
            'email.email' => 'فرمت ایمیل معتبر نیست.',
            'password.required' => 'واردکردن رمز عبور الزامی است.',
        ]);

        $throttleKey = $this->throttleKey(
            $validated['email'],
            $request
        );

        // جلوگیری از حمله حدس رمز
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            throw ValidationException::withMessages([
                'email' => "تعداد تلاش‌های ورود بیش از حد مجاز است. {$seconds} ثانیه دیگر دوباره امتحان کنید.",
            ]);
        }

        /*
         * فقط حساب مدیرِ بن‌نشده اجازه ورود دارد.
         * پیام خطا عمداً عمومی است تا وجود حساب مدیر افشا نشود.
         */
        $authenticated = Auth::attempt([
            'email' => $validated['email'],
            'password' => $validated['password'],
            'role' => 'admin',
            'banned' => false,
        ], $request->boolean('remember'));

        if (! $authenticated) {
            RateLimiter::hit($throttleKey, 60);

            throw ValidationException::withMessages([
                'email' => 'اطلاعات ورود مدیریت صحیح نیست.',
            ]);
        }

        RateLimiter::clear($throttleKey);

        // جلوگیری از Session Fixation
        $request->session()->regenerate();

        $admin = $request->user();

        $admin->last_login_at = now();
        $admin->save();

        return redirect()->intended(
            route('admin.dashboard')
        );
    }

    /**
     * تعیین کلید محدودیت ورود با ایمیل و IP
     */
    private function throttleKey(string $email, Request $request): string
    {
        return Str::transliterate(
            Str::lower($email)
        ).'|'.$request->ip();
    }

    /**
     * رفتار در صورت ورود قبلی کاربر
     */
    private function redirectAuthenticatedUser(
        Request $request
    ): RedirectResponse {
        $user = $request->user();

        if ((bool) $user->banned) {
            Auth::logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->route('admin.login')
                ->withErrors([
                    'email' => 'این حساب کاربری مسدود شده است.',
                ]);
        }

        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        return redirect()
            ->route('home')
            ->with(
                'error',
                'برای ورود به مدیریت، ابتدا از حساب کاربری فعلی خارج شوید.'
            );
    }
}