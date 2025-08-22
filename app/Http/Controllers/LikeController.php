<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Like;

class LikeController extends Controller
{
    public function store($likedUserId)
    {
        $user = Auth::user();

        // اگر خودت را لایک نکنی
        if ($user->id == $likedUserId) {
            return back()->with('error', 'نمی‌توانید خودتان را لایک کنید.');
        }

        Like::firstOrCreate([
            'user_id' => $user->id,
            'liked_user_id' => $likedUserId,
        ]);

        return back()->with('success', 'کاربر لایک شد.');
    }

    // The following index() method is commented out to avoid redeclaration error.
    // public function index()
    // {
    //     $user = Auth::user();

    //     return view('likes.index', [
    //         'likedUsers' => $user->likedUsers, // لیست لایک‌های من
    //         'likedByUsers' => $user->likedByUsers // لیست کسانی که من را لایک کرده‌اند
    //     ]);
    // }

    public function index()
    {
        $user = Auth::user();

        // افرادی که من لایک کرده‌ام
        $likedUsers = auth()->user()->likedUsers;
        // کاربران لایک شده توسط کاربر جاری


        // افرادی که من را لایک کرده‌اند
        $likedByUsers = auth()->user()->likedByUsers;

        // کاربران لایک شده توسط کاربر جاری


        return view('likes.index', compact('likedUsers', 'likedByUsers'));
    }
}
