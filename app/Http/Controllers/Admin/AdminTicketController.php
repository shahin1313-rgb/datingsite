<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Services\AdminAuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AdminTicketController extends Controller
{
    public function __construct(private readonly AdminAuditLogger $auditLogger) {}

    public function index(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', Rule::in(['open', 'answered', 'closed'])],
        ]);

        $tickets = Ticket::query()
            ->whereNull('parent_id')
            ->with('user')
            ->withCount('replies')
            ->when($filters['status'] ?? null, fn ($q, $status) => $q->where('status', $status))
            ->when($filters['q'] ?? null, function ($q, $term): void {
                $q->where(function ($nested) use ($term): void {
                    $nested->where('subject', 'like', '%'.$term.'%')
                        ->orWhere('message', 'like', '%'.$term.'%')
                        ->orWhereHas('user', fn ($user) => $user->where('name', 'like', '%'.$term.'%'));
                });
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.tickets.index', compact('tickets'));
    }

    public function show(Ticket $ticket): View
    {
        abort_if($ticket->parent_id !== null, 404);
        $ticket->load(['user', 'replies.user']);

        return view('admin.tickets.show', compact('ticket'));
    }

    public function reply(Request $request, Ticket $ticket): RedirectResponse
    {
        $validated = $request->validate(['message' => ['required', 'string', 'max:5000']]);
        abort_if($ticket->parent_id !== null, 404);

        DB::transaction(function () use ($request, $ticket, $validated): void {
            $locked = Ticket::query()->lockForUpdate()->findOrFail($ticket->id);
            if ($locked->status === 'closed') {
                throw ValidationException::withMessages(['message' => 'تیکت بسته‌شده قابل پاسخ نیست.']);
            }

            $before = $locked->status;
            Ticket::query()->create([
                'user_id' => $request->user()->id,
                'parent_id' => $locked->id,
                'subject' => 'پاسخ به: '.$locked->subject,
                'message' => $validated['message'],
                'status' => 'answered',
            ]);
            $locked->update(['status' => 'answered']);

            $this->auditLogger->record(
                request: $request,
                actor: $request->user(),
                target: $locked->user,
                action: 'ticket.replied',
                before: ['ticket_id' => $locked->id, 'status' => $before],
                after: ['ticket_id' => $locked->id, 'status' => 'answered'],
            );
        });

        return redirect()->route('admin.tickets.show', $ticket)->with('success', 'پاسخ ثبت شد.');
    }

    public function close(Request $request, Ticket $ticket): RedirectResponse
    {
        abort_if($ticket->parent_id !== null, 404);

        DB::transaction(function () use ($request, $ticket): void {
            $locked = Ticket::query()->lockForUpdate()->findOrFail($ticket->id);
            if ($locked->status === 'closed') {
                throw ValidationException::withMessages(['ticket' => 'این تیکت قبلاً بسته شده است.']);
            }
            $before = $locked->status;
            $locked->update(['status' => 'closed']);
            $this->auditLogger->record(
                request: $request,
                actor: $request->user(),
                target: $locked->user,
                action: 'ticket.closed',
                before: ['ticket_id' => $locked->id, 'status' => $before],
                after: ['ticket_id' => $locked->id, 'status' => 'closed'],
            );
        });

        return redirect()->route('admin.tickets.show', $ticket)->with('success', 'تیکت بسته شد.');
    }
}
