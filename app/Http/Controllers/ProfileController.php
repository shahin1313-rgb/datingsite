<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\ProfileView;


class ProfileController extends Controller
{
    // Show profile edit form
    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    // Update profile
    public function update(Request $request)
    {
        $user = Auth::user();

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'city' => 'nullable|string|max:255',
            'bio' => 'nullable|string|max:1000',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('profile_picture')) {
            $path = $request->file('profile_picture')->store('profile_pictures', 'public');
            $validatedData['profile_picture'] = $path;
        }

        $user->update($validatedData);

        return redirect()->route('dashboard')->with('success', 'پروفایل با موفقیت بروزرسانی شد.');
    }

    // Show user profile and log visit
    public function show($id)
    {
        $profileOwner = User::findOrFail($id);

        if (Auth::check() && Auth::id() !== $profileOwner->id) {
            ProfileView::create([
                'viewer_id' => Auth::id(),
                'profile_owner_id' => $profileOwner->id,
            ]);
        }

        return view('profile.show', compact('profileOwner'));
    }

    // Search profiles
    public function search(Request $request)
    {
        $city = $request->input('city');
        $minAge = $request->input('min_age', 18);
        $maxAge = $request->input('max_age', 99);

        $query = User::query();

        if ($city) {
            $query->where('city', 'like', '%' . $city . '%');
        }

        if ($minAge && $maxAge) {
            $currentYear = Carbon::now()->year;
            $minBirthYear = $currentYear - $maxAge;
            $maxBirthYear = $currentYear - $minAge;

            $query->whereBetween('birth_year', [$minBirthYear, $maxBirthYear]);
        }

        if ($request->filled('marital_status')) {
            $query->where('marital_status', $request->marital_status);
        }

        if ($request->filled('interested_in')) {
            $query->where('interests', 'like', '%' . $request->interests . '%');
        }

        if ($request->has('has_photo')) {
            $query->whereNotNull('profile_picture');
        }

        if ($request->has('is_active')) {
            $query->where('is_active', true);
        }

        $profiles = $query->paginate(3);

        return view('search', compact('profiles'));
    }
}
