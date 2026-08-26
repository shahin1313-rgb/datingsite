<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Message;
use App\Services\ProfilePhotoService;

class PhotoController extends Controller
{
    public function index()
    {
        $users = User::whereNotNull('profile_picture')->get();
        return view('admin.photos.index', compact('users'));
    }

    public function destroy(
        $id,
        ProfilePhotoService $photos
    )
    {
        $user = User::findOrFail($id);
        $picturePath = $user->profile_picture;

        $user->profile_picture = null;
        $user->save();

        $photos->delete($picturePath);

        // ارسال پیام به کاربر
        Message::create([
            'sender_id' => auth()->id(), // ادمین
            'receiver_id' => $user->id,
            'message' => 'عکس شما به دلیل نامناسب بودن توسط مدیریت حذف شد.',
        ]);
        return redirect()->route('admin.photos.index')->with('success', 'تصویر حذف شد.');
    }
}
