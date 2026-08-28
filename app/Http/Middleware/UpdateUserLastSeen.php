<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UpdateUserLastSeen
{
    /**
     * ثبت فعالیت کاربر، حداکثر یک‌بار در هر دقیقه.
     */
    public function handle(
        Request $request,
        Closure $next
    ): Response {
        $user = $request->user();

        if (
            $user instanceof User &&
            (
                $user->last_seen_at === null ||
                $user->last_seen_at->lte(
                    now()->subSeconds(
                        User::PRESENCE_WRITE_INTERVAL_SECONDS
                    )
                )
            )
        ) {
            $seenAt = now();

            /*
             * ثبت حضور نباید updated_at پروفایل را تغییر دهد؛
             * چون بازدید ساده کاربر، ویرایش پروفایل نیست.
             */
            $user->newModelQuery()
                ->whereKey($user->getKey())
                ->toBase()
                ->update([
                    'last_seen_at' => $seenAt,
                ]);

            $user->setAttribute(
                'last_seen_at',
                $seenAt
            );

            $user->syncOriginalAttribute(
                'last_seen_at'
            );
        }

        return $next($request);
    }
}
