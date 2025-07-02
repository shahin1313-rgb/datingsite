<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
// use bdbdIlluminate\Foundation\Auth\User;
use Illuminate\Database\Eloquent\Model;

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
        // dd($user);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'city' => 'nullable|string|max:255',
            'bio' => 'nullable|string|max:1000',
        ]);

        $user->update($request->only('name', 'email', 'city', 'bio'));

        return redirect()->route('dashboard')->with('success', 'Profile updated successfully.');
    }



    public function show($id)
    {
        $user = User::findOrFail($id); // Fetch the user or return 404 if not found
        return view('profile', compact('user')); // Pass user data to the profile view
    }

    public function search(Request $request)
    {
        // Get the search parameters from the request
        $city = $request->input('city');
        $minAge = $request->input('min_age');
        $maxAge = $request->input('max_age');

        // Start building the query
        $query = User::query();

        // Filter by city (if provided)
        if ($city) {
            $query->where('city', 'like', '%' . $city . '%');
        }


if ($minAge && $maxAge) {
    $query->whereBetween('age', [$minAge, $maxAge]);
}

        // Execute the query and paginate the results
        $profiles = $query->paginate(3);

        // Pass the results to the view
        return view('search', compact('profiles'));
    }
}
