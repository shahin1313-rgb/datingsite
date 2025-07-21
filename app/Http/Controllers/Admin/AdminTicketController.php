<?php



namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ticket;
use Illuminate\Support\Facades\Auth;

class AdminTicketController extends Controller
{
    // لیست همه تیکت‌ها
    public function index()
    {
        $tickets = Ticket::whereNull('parent_id')
            ->with('user', 'replies.user')
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('admin.tickets.index', compact('tickets'));
    }

    // نمایش جزئیات یک تیکت
    public function show($id)
    {
        $ticket = Ticket::with('user', 'replies.user')->findOrFail($id);
        return view('admin.tickets.show', compact('ticket'));
    }

    // پاسخ به تیکت
    public function reply(Request $request, $ticketId)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $ticket = Ticket::findOrFail($ticketId);

        Ticket::create([
            'user_id' => Auth::id(), // ادمین پاسخ می‌دهد
            'parent_id' => $ticket->id,
            'subject' => 'پاسخ به: ' . $ticket->subject,
            'message' => $request->message,
        ]);

        return back()->with('success', 'پاسخ شما با موفقیت ارسال شد.');
    }

    // تغییر وضعیت (مثلاً بستن تیکت)
    public function close($id)
    {
        $ticket = Ticket::findOrFail($id);
        $ticket->status = 'closed';
        $ticket->save();

        return back()->with('success', 'تیکت بسته شد.');
    }
}
