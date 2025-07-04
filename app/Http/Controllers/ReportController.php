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
        $reports = Report::with(['reporter', 'reported'])
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('admin.reports', compact('reports'));
    }
    public function destroy($id)
    {
        Report::findOrFail($id)->delete();
        return redirect()->route('admin.reports')->with('success', 'Report deleted.');
    }

    public function resolve(Report $report)
    {
        $report->status = 'resolved';
        $report->save();

        return redirect()->back()->with('status', 'گزارش بررسی شد.');
    }
    public function store(Request $request)
    {


        $request->validate([
            'reported_id' => 'required|exists:users,id',
        ]);




        Report::create([
            'reporter_id' => Auth::id(),
            'reported_id' => $request->reported_id,
            'reason' => $request->reason,
        ]);



        return back()->with('success', 'User reported successfully.');
    }
}
