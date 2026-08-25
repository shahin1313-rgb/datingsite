<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\AdminTwoFactorCode;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Throwable;

class AdminLoginController extends Controller
{
    /*
     * یک Hash ثابت برای یکسان‌کردن زمان پاسخ، زمانی که ایمیل وجود ندارد.
     */
    private const DUMMY_PASSWORD_HASH =
        '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.';

    public function showLoginForm(
        Request $request
    ): View|RedirectResponse {
        if ($request->user()) {
            return $this->redirectAuthenticatedUser($request);
        }

        if (
            $request->session()->has(
                'admin_2fa_user_id'
            )
        ) {
            return redirect()->route(
                'admin.two-factor.form'
            );
        }

        return view('admin.auth.login');
    }

    public function login(
        Request $request
    ): RedirectResponse {
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
            'email.required' =>
                'واردکردن ایمیل الزامی است.',

            'email.email' =>
                'فرمت ایمیل معتبر نیست.',

            'password.required' =>
                'واردکردن رمز عبور الزامی است.',
        ]);

        $throttleKey = $this->throttleKey(
            $validated['email'],
            $request
        );

        if (
            RateLimiter::tooManyAttempts(
                $throttleKey,
                5
            )
        ) {
            $seconds = RateLimiter::availableIn(
                $throttleKey
            );

            throw ValidationException::withMessages([
                'email' =>
                    "تعداد تلاش‌های ورود بیش از حد مجاز است. {$seconds} ثانیه دیگر دوباره امتحان کنید.",
            ]);
        }

        $admin = User::query()
            ->where(
                'email',
                $validated['email']
            )
            ->first();

        $passwordIsValid = Hash::check(
            $validated['password'],
            $admin?->password
                ?? self::DUMMY_PASSWORD_HASH
        );

        if (
            ! $admin ||
            ! $passwordIsValid ||
            $admin->role !== 'admin' ||
            (bool) $admin->banned
        ) {
            RateLimiter::hit(
                $throttleKey,
                60
            );

            throw ValidationException::withMessages([
                'email' =>
                    'اطلاعات ورود مدیریت صحیح نیست.',
            ]);
        }

        RateLimiter::clear($throttleKey);

        $code = (string) random_int(
            100000,
            999999
        );

        $admin->forceFill([
            'admin_two_factor_code_hash' =>
                Hash::make($code),

            'admin_two_factor_expires_at' =>
                now()->addMinutes(5),
        ])->save();

        /*
         * جلوگیری از Session Fixation.
         */
        $request->session()->regenerate();

        $request->session()->forget(
            'admin_2fa_verified_at'
        );

        $request->session()->put([
            'admin_2fa_user_id' =>
                $admin->id,

            'admin_2fa_remember' =>
                $request->boolean('remember'),
        ]);

        try {
            $admin->notify(
                new AdminTwoFactorCode($code)
            );
        } catch (Throwable $exception) {
            /*
             * اگر ایمیل ارسال نشد، ورود بدون 2FA
             * نباید امکان‌پذیر باشد.
             */
            $admin->forceFill([
                'admin_two_factor_code_hash' =>
                    null,

                'admin_two_factor_expires_at' =>
                    null,
            ])->save();

            $request->session()->forget([
                'admin_2fa_user_id',
                'admin_2fa_remember',
            ]);

            Log::error(
                'Admin two-factor email could not be sent.',
                [
                    'admin_id' => $admin->id,
                    'exception' => $exception,
                ]
            );

            return back()
                ->withInput(
                    $request->only('email')
                )
                ->withErrors([
                    'email' =>
                        'ارسال کد تأیید ممکن نشد. تنظیمات ایمیل سرور را بررسی کنید.',
                ]);
        }

        return redirect()
            ->route('admin.two-factor.form')
            ->with(
                'status',
                'کد تأیید به ایمیل مدیر ارسال شد.'
            );
    }

    private function throttleKey(
        string $email,
        Request $request
    ): string {
        return Str::transliterate(
            Str::lower($email)
        ).'|'.$request->ip();
    }

    private function redirectAuthenticatedUser(
        Request $request
    ): RedirectResponse {
        $user = $request->user();

        if ((bool) $user->banned) {
            return $this->logoutAndRedirect(
                $request,
                'این حساب کاربری مسدود شده است.'
            );
        }

        if ($user->role === 'admin') {
            if (
                $request->session()->has(
                    'admin_2fa_verified_at'
                )
            ) {
                return redirect()->route(
                    'admin.dashboard'
                );
            }

            return $this->logoutAndRedirect(
                $request,
                'برای ورود به مدیریت، تأیید دومرحله‌ای را تکمیل کنید.'
            );
        }

        return redirect()
            ->route('home')
            ->with(
                'error',
                'برای ورود به مدیریت، ابتدا از حساب کاربری فعلی خارج شوید.'
            );
    }

    private function logoutAndRedirect(
        Request $request,
        string $message
    ): RedirectResponse {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('admin.login')
            ->withErrors([
                'email' => $message,
            ]);
    }
}