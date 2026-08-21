<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    /**
     * نمایش تیکت‌های متعلق به کاربر جاری.
     */
    public function index(Request $request)
    {
        $tickets = $request->user()
            ->tickets()
            ->whereNull('parent_id')
            ->with(['user', 'replies.user'])
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('user.dashboardticket', compact('tickets'));
    }

    /**
     * نمایش فرم ایجاد تیکت.
     */
    public function create()
    {
        return view('user.dashboardticketcreate');
    }

    /**
     * ذخیره تیکت جدید برای کاربر جاری.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $request->user()->tickets()->create($validated);

        return back()->with('success', 'تیکت با موفقیت ارسال شد.');
    }

    /**
     * حذف تیکت فقط توسط صاحب آن.
     */
    public function destroy(Request $request, int $id)
    {
        $ticket = $request->user()
            ->tickets()
            ->whereNull('parent_id')
            ->findOrFail($id);

        $ticket->delete();

        return redirect()
            ->route('user.tickets.index')
            ->with('success', 'تیکت حذف شد.');
    }

    /**
     * ارسال پاسخ فقط برای تیکت متعلق به کاربر جاری.
     */
    public function reply(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'message' => 'required|string',
        ]);

        $ownedTicket = $request->user()
            ->tickets()
            ->whereNull('parent_id')
            ->findOrFail($ticket->getKey());

        $request->user()->tickets()->create([
            'subject' => 'پاسخ به: ' . $ownedTicket->subject,
            'message' => $validated['message'],
            'parent_id' => $ownedTicket->id,
        ]);

        return back()->with('success', 'پاسخ شما ارسال شد.');
    }
}