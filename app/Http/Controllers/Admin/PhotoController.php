<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Message;
use Illuminate\Support\Facades\Storage;

class PhotoController extends Controller
{
    public function index()
    {
        $users = User::whereNotNull('profile_picture')->get();
        return view('admin.photos.index', compact('users'));
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if ($user->profile_picture && Storage::disk('public')->exists($user->profile_picture)) {
            Storage::disk('public')->delete($user->profile_picture);
        }

        $user->profile_picture = null;
        $user->save();

        // ارسال پیام به کاربر
        Message::create([
            'sender_id' => auth()->id(), // ادمین
            'receiver_id' => $user->id,
            'message' => 'عکس شما به دلیل نامناسب بودن توسط مدیریت حذف شد.',
        ]);
        return redirect()->route('admin.photos.index')->with('success', 'تصویر حذف شد.');
    }
}
