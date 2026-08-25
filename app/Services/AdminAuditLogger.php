<?php

namespace App\Services;

use App\Models\AdminAuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminAuditLogger
{
    /**
     * @param array<string, mixed>|null $before
     * @param array<string, mixed>|null $after
     */
    public function record(
        Request $request,
        User $actor,
        ?User $target,
        string $action,
        ?array $before = null,
        ?array $after = null,
    ): AdminAuditLog {
        return AdminAuditLog::query()->create([
            'actor_id' => $actor->id,
            'actor_email' => $actor->email,

            'target_user_id' => $target?->id,
            'target_email' => $target?->email,

            'action' => $action,

            'before' => $before,
            'after' => $after,

            'ip_address' => $request->ip(),

            'user_agent' => Str::limit(
                (string) $request->userAgent(),
                1000,
                ''
            ),
        ]);
    }
}