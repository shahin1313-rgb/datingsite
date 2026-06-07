<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ProfileView;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class ProfileController extends Controller
{
    public function edit()
    {
        return view('profile.edit', ['user' => Auth::user()]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name'            => 'required|string|max:255',
            'email'           => 'required|email|max:255|unique:users,email,' . $user->id,
            'city'            => 'nullable|string|max:255',
            'bio'             => 'nullable|string|max:1000',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('profile_picture')) {
            // حذف عکس قبلی (اگر وجود داشته باشد)
            if ($user->profile_picture) {
                Storage::disk('public')->delete($user->profile_picture);
            }

            $path = $request->file('profile_picture')->store('profile_pictures', 'public');
            $validated['profile_picture'] = $path;
        }

        $user->update($validated);

        return redirect()->route('dashboard')->with('success', 'پروفایل با موفقیت بروزرسانی شد.');
    }

    public function show($id)
{
    // ۱. تغییر نام متغیر به $user
    $user = User::findOrFail($id);

    // فقط اگر کاربر لاگین کرده باشد و پروفایل متعلق به خودش نباشد → بازدید ثبت شود
    if (Auth::check() && Auth::id() !== $user->id) {

        $alreadyViewedToday = ProfileView::where('viewer_id', Auth::id())
            ->where('viewed_id', $user->id)
            ->whereDate('created_at', Carbon::today())
            ->exists();

        if (! $alreadyViewedToday) {
            ProfileView::create([
                'viewer_id' => Auth::id(),
                'viewed_id' => $user->id,
            ]);
        }
    }

    // ۲. پاس دادن متغیر با نام 'user' به فایل بلید
    return view('profile.show', compact('user'));
}
    public function search(Request $request)
    {
        $query = User::query();

        if ($request->filled('city')) {
            $query->where('city', 'like', '%' . $request->city . '%');
        }

        if ($request->filled('min_age') || $request->filled('max_age')) {
            $minAge = $request->input('min_age', 18);
            $maxAge = $request->input('max_age', 99);

            $currentYear   = Carbon::now()->year;
            $minBirthYear  = $currentYear - $maxAge; // کسی که maxAge داره → سال تولد کمتر
            $maxBirthYear  = $currentYear - $minAge;

            $query->whereBetween('birth_year', [$minBirthYear, $maxBirthYear]);
        }

        if ($request->filled('marital_status')) {
            $query->where('marital_status', $request->marital_status);
        }

        // اصلاح نام فیلد
        if ($request->filled('interested_in')) {
            $query->where('interests', 'like', '%' . $request->interested_in . '%');
        }

        if ($request->has('has_photo')) {
            $query->whereNotNull('profile_picture');
        }

        if ($request->has('is_active')) {
            $query->where('is_active', true);
        }

        $profiles = $query->paginate(12); // تعداد منطقی‌تر

        return view('search', compact('profiles'));
    }
}