<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AdminAuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AdminTwoFactorController extends Controller
{
    public function __construct(
        private readonly AdminAuditLogger $auditLogger
    ) {
    }

    public function show(
        Request $request
    ): View|RedirectResponse {
        if (
            $request->user()?->role === 'admin' &&
            $request->session()->has(
                'admin_2fa_verified_at'
            )
        ) {
            return redirect()->route(
                'admin.dashboard'
            );
        }

        if (
            ! $request->session()->has(
                'admin_2fa_user_id'
            )
        ) {
            return redirect()->route(
                'admin.login'
            );
        }

        return view(
            'admin.auth.two-factor'
        );
    }

    public function verify(
        Request $request
    ): RedirectResponse {
        $validated = $request->validate([
            'code' => [
                'required',
                'digits:6',
            ],
        ], [
            'code.required' =>
                'کد تأیید را وارد کنید.',

            'code.digits' =>
                'کد تأیید باید دقیقاً ۶ رقم باشد.',
        ]);

        $userId = $request
            ->session()
            ->get('admin_2fa_user_id');

        if (! $userId) {
            return redirect()->route(
                'admin.login'
            );
        }

        $throttleKey =
            'admin-2fa:'.$userId.'|'.$request->ip();

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
                'code' =>
                    "تعداد تلاش‌ها بیش از حد مجاز است. {$seconds} ثانیه دیگر دوباره امتحان کنید.",
            ]);
        }

        $remember = (bool) $request
            ->session()
            ->get(
                'admin_2fa_remember',
                false
            );

        /*
         * lockForUpdate از استفاده هم‌زمان و مجدد
         * از یک کد جلوگیری می‌کند.
         */
        $admin = DB::transaction(
            function () use (
                $request,
                $userId,
                $validated,
                $throttleKey
            ): User {
                $admin = User::query()
                    ->lockForUpdate()
                    ->find($userId);

                if (
                    ! $this->hasValidChallenge(
                        $admin,
                        $validated['code']
                    )
                ) {
                    RateLimiter::hit(
                        $throttleKey,
                        300
                    );

                    throw ValidationException::withMessages([
                        'code' =>
                            'کد تأیید اشتباه یا منقضی شده است.',
                    ]);
                }

                /*
                 * بعد از اولین استفاده، کد فوراً باطل می‌شود.
                 */
                $admin->forceFill([
                    'admin_two_factor_code_hash' =>
                        null,

                    'admin_two_factor_expires_at' =>
                        null,

                    'last_login_at' =>
                        now(),
                ])->save();

                $this->auditLogger->record(
                    request: $request,
                    actor: $admin,
                    target: $admin,
                    action:
                        'admin.login.two_factor_verified',
                    before: null,
                    after: [
                        'two_factor_verified' =>
                            true,
                    ],
                );

                return $admin;
            }
        );

        RateLimiter::clear($throttleKey);

        $request->session()->forget([
            'admin_2fa_user_id',
            'admin_2fa_remember',
        ]);

        Auth::login(
            $admin,
            $remember
        );

        $request->session()->regenerate();

        $request->session()->put(
            'admin_2fa_verified_at',
            now()->timestamp
        );

        return redirect()->intended(
            route('admin.dashboard')
        );
    }

    public function cancel(
        Request $request
    ): RedirectResponse {
        $userId = $request
            ->session()
            ->pull('admin_2fa_user_id');

        $request->session()->forget(
            'admin_2fa_remember'
        );

        if ($userId) {
            User::query()
                ->whereKey($userId)
                ->update([
                    'admin_two_factor_code_hash' =>
                        null,

                    'admin_two_factor_expires_at' =>
                        null,
                ]);
        }

        $request->session()->regenerate();

        return redirect()->route(
            'admin.login'
        );
    }

    private function hasValidChallenge(
        ?User $admin,
        string $code
    ): bool {
        if (
            ! $admin ||
            $admin->role !== 'admin' ||
            (bool) $admin->banned ||
            ! $admin->admin_two_factor_code_hash ||
            ! $admin->admin_two_factor_expires_at ||
            $admin
                ->admin_two_factor_expires_at
                ->isPast()
        ) {
            return false;
        }

        return Hash::check(
            $code,
            $admin->admin_two_factor_code_hash
        );
    }
}