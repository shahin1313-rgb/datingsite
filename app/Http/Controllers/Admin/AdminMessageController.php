<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Services\AdminAuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminMessageController extends Controller
{
    private const ACCESS_UNTIL = 'admin_private_messages_access_until';
    private const ACCESS_REASON = 'admin_private_messages_access_reason';

    public function __construct(private readonly AdminAuditLogger $auditLogger) {}

    public function index(Request $request): View
    {
        if (! $this->accessGranted($request)) {
            $request->session()->forget([self::ACCESS_UNTIL, self::ACCESS_REASON]);
            return view('admin.messages.index', ['accessGranted' => false, 'messages' => null, 'accessReason' => null]);
        }

        $filters = $request->validate([
            'sender' => ['nullable', 'string', 'max:100'],
            'receiver' => ['nullable', 'string', 'max:100'],
            'date' => ['nullable', 'date_format:Y-m-d'],
        ]);

        $query = Message::query()->with(['sender', 'receiver'])->latest();
        if (! empty($filters['sender'])) {
            $query->whereHas('sender', fn ($q) => $q->where('name', 'like', '%'.$filters['sender'].'%'));
        }
        if (! empty($filters['receiver'])) {
            $query->whereHas('receiver', fn ($q) => $q->where('name', 'like', '%'.$filters['receiver'].'%'));
        }
        if (! empty($filters['date'])) {
            $query->whereDate('created_at', $filters['date']);
        }

        $messages = $query->paginate(20)->withQueryString();
        $reason = (string) $request->session()->get(self::ACCESS_REASON);
        $this->auditLogger->record(
            request: $request,
            actor: $request->user(),
            target: null,
            action: 'private_messages.viewed',
            after: ['reason' => $reason, 'filters' => array_filter($filters), 'page' => $messages->currentPage(), 'result_count' => $messages->count()],
        );

        return view('admin.messages.index', ['accessGranted' => true, 'messages' => $messages, 'accessReason' => $reason]);
    }

    public function grantAccess(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'reason' => ['required', 'string', 'min:10', 'max:500'],
            'current_password' => ['required', 'current_password'],
        ], [
            'reason.min' => 'دلیل دسترسی باید دست‌کم ۱۰ نویسه باشد.',
            'current_password.current_password' => 'رمز عبور مدیر صحیح نیست.',
        ]);

        $request->session()->put([
            self::ACCESS_UNTIL => now()->addMinutes(15)->timestamp,
            self::ACCESS_REASON => $validated['reason'],
        ]);
        $this->auditLogger->record(
            request: $request,
            actor: $request->user(),
            target: null,
            action: 'private_messages.access_granted',
            after: ['reason' => $validated['reason'], 'duration_minutes' => 15],
        );

        return redirect()->route('admin.messages');
    }

    public function revokeAccess(Request $request): RedirectResponse
    {
        $reason = $request->session()->get(self::ACCESS_REASON);
        $request->session()->forget([self::ACCESS_UNTIL, self::ACCESS_REASON]);
        $this->auditLogger->record(
            request: $request,
            actor: $request->user(),
            target: null,
            action: 'private_messages.access_revoked',
            before: ['reason' => $reason],
        );

        return redirect()->route('admin.messages');
    }

    private function accessGranted(Request $request): bool
    {
        $reason = $request->session()->get(self::ACCESS_REASON);

        return (int) $request->session()->get(self::ACCESS_UNTIL, 0) > now()->timestamp
            && is_string($reason)
            && trim($reason) !== '';
    }
}
