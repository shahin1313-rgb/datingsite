<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\User;
use App\Services\AdminAuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function __construct(
        private readonly AdminAuditLogger $auditLogger
    ) {
    }

    public function index(): View
    {
        $users = User::query()
            ->latest()
            ->paginate(10);

        return view(
            'admin.dashboard',
            compact('users')
        );
    }

    public function makeAdmin(
        Request $request,
        User $user
    ): RedirectResponse {
        $request->validate([
            'current_password' => [
                'required',
                'string',
                'current_password:web',
            ],
        ], [
            'current_password.required' =>
                'برای ارتقای کاربر، رمز عبور فعلی مدیر را وارد کنید.',

            'current_password.current_password' =>
                'رمز عبور فعلی مدیر صحیح نیست.',
        ]);

        DB::transaction(
            function () use (
                $request,
                $user
            ): void {
                /*
                 * قفل تمام مدیران برای جلوگیری از عملیات هم‌زمان.
                 */
                User::query()
                    ->where('role', 'admin')
                    ->lockForUpdate()
                    ->get(['id']);

                $actor = User::query()->find(
                    $request->user()->id
                );

                if (
                    ! $actor ||
                    $actor->role !== 'admin' ||
                    (bool) $actor->banned ||
                    ! Hash::check(
                        (string) $request->input(
                            'current_password'
                        ),
                        $actor->password
                    )
                ) {
                    throw ValidationException::withMessages([
                        'admin_action' =>
                            'مجوز مدیریتی شما تغییر کرده است؛ دوباره وارد شوید.',
                    ]);
                }

                $target = User::query()
                    ->lockForUpdate()
                    ->findOrFail($user->id);

                if ((bool) $target->banned) {
                    throw ValidationException::withMessages([
                        'admin_action' =>
                            'ابتدا حساب کاربر را از حالت مسدود خارج کنید.',
                    ]);
                }

                if ($target->role === 'admin') {
                    throw ValidationException::withMessages([
                        'admin_action' =>
                            'این حساب از قبل مدیر است.',
                    ]);
                }

                $before =
                    $this->securitySnapshot($target);

                $target->forceFill([
                    'role' => 'admin',
                ])->save();

                $after = $this->securitySnapshot(
                    $target->fresh()
                );

                $this->auditLogger->record(
                    request: $request,
                    actor: $actor,
                    target: $target,
                    action:
                        'user.promoted_to_admin',
                    before: $before,
                    after: $after,
                );
            },
            3
        );

        return redirect()
            ->route('admin.users')
            ->with(
                'status',
                'کاربر با موفقیت به مدیر ارتقا یافت.'
            );
    }

    public function showReports(): View
    {
        $reports = Report::query()
            ->latest()
            ->get();

        return view(
            'admin.reports',
            compact('reports')
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function securitySnapshot(
        User $user
    ): array {
        return [
            'id' => $user->id,
            'email' => $user->email,
            'role' => $user->role,
            'banned' => (bool) $user->banned,
        ];
    }
}