<?php

namespace App\Http\Controllers;

use App\Models\Like;
use App\Models\User;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    public function store(
        Request $request,
        int $likedUserId
    )
    {
        $user = $request->user();

        if ($user->id === $likedUserId) {
            return back()->with('error', 'نمی‌توانید خودتان را لایک کنید.');
        }

        /*
         * شناسه مقصد به تنهایی قابل اعتماد نیست. مقصد باید یک
         * کاربر عمومی باشد و هیچ بلاکی در هیچ‌یک از دو جهت وجود نداشته باشد.
         */
        User::query()
            ->discoverableBy($user)
            ->findOrFail($likedUserId);

        Like::firstOrCreate([
            'user_id' => $user->id,
            'liked_user_id' => $likedUserId,
        ]);

        $isMatch = Like::query()
            ->where('user_id', $likedUserId)
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

    public function index(Request $request)
    {
        $user = $request->user();

        $likedUsers = $user
            ->likedUsers()
            ->discoverableBy($user)
            ->latest('likes.created_at')
            ->get();

        $likedByUsers = $user
            ->likedByUsers()
            ->discoverableBy($user)
            ->latest('likes.created_at')
            ->get();

        return view('likes.index', compact('likedUsers', 'likedByUsers'));
    }
}
