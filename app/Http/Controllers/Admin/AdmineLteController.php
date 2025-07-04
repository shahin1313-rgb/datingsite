<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class AdmineLteController extends Controller
{
    public function index()
    {
        return view('admin.dashboard');
    }

    public function indexUser()
    {
        $users = User::paginate(10); // Fetch users with pagination
        return view('admin.user.index', compact('users'));
    }

    public function ban($id)
    {
        $user = User::findOrFail($id);
        $user->banned = !$user->banned;
        $user->save();

        return redirect()->route('admin.users')->with('status', 'وضعیت کاربر با موفقیت تغییر کرد.');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->back()->with('status', 'کاربر حذف شد.');
    }
}
