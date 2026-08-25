<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AdminAuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AdmineLteController extends Controller
{
    public function __construct(
        private readonly AdminAuditLogger $auditLogger
    ) {
    }

    public function index(): View
    {
        return view('admin.dashboard');
    }

    public function indexUser(
        Request $request
    ): View {
        $query = User::query();

        if ($request->filled('name')) {
            $query->where(
                'name',
                'like',
                '%'.$request->string('name').'%'
            );
        }

        if ($request->filled('email')) {
            $query->where(
                'email',
                'like',
                '%'.$request->string('email').'%'
            );
        }

        if ($request->filled('banned')) {
            $query->where(
                'banned',
                $request->boolean('banned')
            );
        }

        $users = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $activeAdminCount = User::query()
            ->where('role', 'admin')
            ->where('banned', false)
            ->count();

        return view(
            'admin.user.index',
            compact(
                'users',
                'activeAdminCount'
            )
        );
    }

    public function showUser(
        User $user
    ): View {
        $activeAdminCount = User::query()
            ->where('role', 'admin')
            ->where('banned', false)
            ->count();

        return view(
            'admin.user.show',
            compact(
                'user',
                'activeAdminCount'
            )
        );
    }

    public function ban(
        Request $request,
        User $user
    ): RedirectResponse {
        $this->confirmCurrentPassword(
            $request
        );

        DB::transaction(
            function () use (
                $request,
                $user
            ): void {
                /*
                 * تمام ردیف‌های مدیران قفل می‌شوند تا دو مدیر
                 * هم‌زمان نتوانند آخرین مدیر را حذف یا مسدود کنند.
                 */
                $this->lockAllAdministrators();

                $actor = $this->freshActiveActor(
                    $request
                );

                $target = User::query()
                    ->lockForUpdate()
                    ->findOrFail($user->id);

                if ($actor->is($target)) {
                    throw ValidationException::withMessages([
                        'admin_action' =>
                            'شما نمی‌توانید وضعیت حساب مدیریتی خودتان را تغییر دهید.',
                    ]);
                }

                $willBeBanned =
                    ! (bool) $target->banned;

                if ($willBeBanned) {
                    $this
                        ->ensureTargetIsNotLastActiveAdmin(
                            $target
                        );
                }

                $before =
                    $this->securitySnapshot($target);

                $target->forceFill([
                    'banned' => $willBeBanned,
                ])->save();

                $after = $this->securitySnapshot(
                    $target->fresh()
                );

                $this->auditLogger->record(
                    request: $request,
                    actor: $actor,
                    target: $target,
                    action: $willBeBanned
                        ? 'user.banned'
                        : 'user.unbanned',
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
                'وضعیت کاربر با موفقیت تغییر کرد.'
            );
    }

    public function destroy(
        Request $request,
        User $user
    ): RedirectResponse {
        $this->confirmCurrentPassword(
            $request
        );

        DB::transaction(
            function () use (
                $request,
                $user
            ): void {
                $this->lockAllAdministrators();

                $actor = $this->freshActiveActor(
                    $request
                );

                $target = User::query()
                    ->lockForUpdate()
                    ->findOrFail($user->id);

                if ($actor->is($target)) {
                    throw ValidationException::withMessages([
                        'admin_action' =>
                            'شما نمی‌توانید حساب مدیریتی خودتان را حذف کنید.',
                    ]);
                }

                $this
                    ->ensureTargetIsNotLastActiveAdmin(
                        $target
                    );

                $before =
                    $this->securitySnapshot($target);

                /*
                 * Audit Log قبل از حذف و داخل همان تراکنش ثبت می‌شود.
                 */
                $this->auditLogger->record(
                    request: $request,
                    actor: $actor,
                    target: $target,
                    action: 'user.deleted',
                    before: $before,
                    after: null,
                );

                $target->delete();
            },
            3
        );

        return redirect()
            ->route('admin.users')
            ->with(
                'status',
                'کاربر حذف شد.'
            );
    }

    private function confirmCurrentPassword(
        Request $request
    ): void {
        $request->validate([
            'current_password' => [
                'required',
                'string',
                'current_password:web',
            ],
        ], [
            'current_password.required' =>
                'برای این عملیات، رمز عبور فعلی مدیر را وارد کنید.',

            'current_password.current_password' =>
                'رمز عبور فعلی مدیر صحیح نیست.',
        ]);
    }

    private function lockAllAdministrators(): void
    {
        User::query()
            ->where('role', 'admin')
            ->lockForUpdate()
            ->get(['id']);
    }

    private function freshActiveActor(
        Request $request
    ): User {
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

        return $actor;
    }

    private function ensureTargetIsNotLastActiveAdmin(
        User $target
    ): void {
        /*
         * حذف مدیر از قبل مسدودشده مشکلی ندارد؛
         * زیرا در شمار مدیران فعال قرار ندارد.
         */
        if (
            $target->role !== 'admin' ||
            (bool) $target->banned
        ) {
            return;
        }

        $activeAdminCount = User::query()
            ->where('role', 'admin')
            ->where('banned', false)
            ->count();

        if ($activeAdminCount <= 1) {
            throw ValidationException::withMessages([
                'admin_action' =>
                    'آخرین مدیر فعال سیستم را نمی‌توان مسدود یا حذف کرد.',
            ]);
        }
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