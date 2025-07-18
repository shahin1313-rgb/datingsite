<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ticket;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    public function index()
    {
        $tickets = Ticket::with('user')->orderByDesc('created_at')->paginate(10);
        return view('user.dashboardticket', compact('tickets'));
    }

    public function create()
    {
        return view('user.dashboardticketcreate');
    }


    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        Ticket::create([
            'user_id' => Auth::id(),
            'subject' => $request->subject,
            'message' => $request->message,
        ]);

        return back()->with('success', 'تیکت با موفقیت ارسال شد.');
    }

    public function destroy($id)
    {
        Ticket::findOrFail($id)->delete();
        return redirect()->route('user.tickets')->with('success', 'تیکت حذف شد.');
    }
}
