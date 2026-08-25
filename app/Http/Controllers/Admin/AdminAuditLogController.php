<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminAuditLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminAuditLogController extends Controller
{
    public function index(
        Request $request
    ): View {
        $query = AdminAuditLog::query()
            ->latest();

        if ($request->filled('action')) {
            $query->where(
                'action',
                (string) $request->string(
                    'action'
                )
            );
        }

        if ($request->filled('email')) {
            $email =
                '%'.$request->string('email').'%';

            $query->where(
                function ($builder) use (
                    $email
                ): void {
                    $builder
                        ->where(
                            'actor_email',
                            'like',
                            $email
                        )
                        ->orWhere(
                            'target_email',
                            'like',
                            $email
                        );
                }
            );
        }

        $logs = $query
            ->paginate(25)
            ->withQueryString();

        $actions = AdminAuditLog::query()
            ->select('action')
            ->distinct()
            ->orderBy('action')
            ->pluck('action');

        return view(
            'admin.audit-logs.index',
            compact(
                'logs',
                'actions'
            )
        );
    }
}