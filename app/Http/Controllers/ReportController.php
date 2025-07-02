<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function index()
    {
        $reports = Report::with('user', 'reported_id')->paginate(10);
        return view('admin.reports', compact('reports'));
    }

    public function destroy($id)
    {
        Report::findOrFail($id)->delete();
        return redirect()->route('admin.reports')->with('success', 'Report deleted.');
    }

    public function store(Request $request)
    {
        $request->validate([
            'reported_id' => 'required|exists:users,id',
            'reason' => 'required|string|max:500',
        ]);

        Report::create([
            'reporter_id' => Auth::id(),
            'reported_id' => $request->reported_id,
            'reason' => $request->reason,
        ]);

        return back()->with('success', 'User reported successfully.');
    }
}
