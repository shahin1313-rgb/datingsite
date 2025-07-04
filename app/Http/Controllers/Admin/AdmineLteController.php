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
        $query = User::query();

        if (request('name')) {
            $query->where('name', 'like', '%' . request('name') . '%');
        }

        if (request('email')) {
            $query->where('email', 'like', '%' . request('email') . '%');
        }

        if (request()->filled('banned')) {
            $query->where('banned', request('banned'));
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('admin.user.index', compact('users'));
    }

    public function showUser(User $user)
    {
        return view('admin.user.show', compact('user'));
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
