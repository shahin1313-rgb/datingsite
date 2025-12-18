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

        // بررسی اینکه آیا طرف مقابل هم قبلا من را لایک کرده؟
    $isMatch = Like::where('user_id', $likedUserId)
                   ->where('liked_user_id', $user->id)
                   ->exists();

    if ($isMatch) {
        return back()->with('success', 'تبریک! شما با هم مچ شدید. پیام بدید!');
    }

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


        // دریافت لیست‌ها به همراه داده‌های پایه برای سرعت بیشتر
    $likedUsers = $user->likedUsers()->latest()->get(); 
    $likedByUsers = $user->likedByUsers()->latest()->get();
        
  
        return view('likes.index', compact('likedUsers', 'likedByUsers'));
    }
}
