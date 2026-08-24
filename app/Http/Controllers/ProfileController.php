<?php

namespace App\Http\Controllers;

use App\Models\ProfileView;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * نمایش فرم ویرایش پروفایل کاربر جاری.
     */
    public function edit()
    {
        return view('profile.edit', [
            'user' => Auth::user(),
        ]);
    }

    /**
     * ذخیره تغییرات پروفایل کاربر جاری.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email,' . $user->id,
            ],

            'city' => [
                'nullable',
                'string',
                'max:255',
            ],

            'bio' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'profile_picture' => [
                'nullable',
                'image',
                'mimes:jpeg,png,jpg,gif',
                'max:2048',
            ],
        ]);

        if ($request->hasFile('profile_picture')) {
            /*
             * ابتدا عکس جدید ذخیره می‌شود.
             */
            $newPicturePath = $request
                ->file('profile_picture')
                ->store('profile_pictures', 'public');

            /*
             * فقط بعد از ذخیره موفق عکس جدید،
             * عکس قبلی حذف می‌شود.
             */
            if ($user->profile_picture) {
                Storage::disk('public')->delete(
                    $user->profile_picture
                );
            }

            $validated['profile_picture'] =
                $newPicturePath;
        }

        $emailChanged =
            $validated['email'] !== $user->email;

        $user->fill($validated);

        /*
         * اگر ایمیل تغییر کرده باشد، تأیید قبلی
         * دیگر معتبر نیست.
         */
        if ($emailChanged) {
            $user->email_verified_at = null;
        }

        $user->save();

        /*
         * برای ایمیل جدید، لینک تأیید تازه ارسال می‌شود.
         */
        if ($emailChanged) {
            $user->sendEmailVerificationNotification();

            return redirect()
                ->route('verification.notice')
                ->with('resent', true);
        }

        return redirect()
            ->route('dashboard')
            ->with(
                'success',
                'پروفایل با موفقیت بروزرسانی شد.'
            );
    }

    /**
     * نمایش عمومی یک پروفایل.
     *
     * اطلاعات خصوصی مانند email و role عمداً
     * از دیتابیس دریافت نمی‌شوند.
     */
    public function show($id)
    {
        $user = User::query()
            ->select([
                'id',
                'name',
                'city',
                'bio',
                'profile_picture',
                'interested_in',
                'salary',
            ])
            ->findOrFail($id);

        /*
         * بازدید از پروفایل خود کاربر ثبت نمی‌شود.
         */
        if (Auth::id() !== $user->id) {
            $alreadyViewedToday = ProfileView::query()
                ->where('viewer_id', Auth::id())
                ->where('viewed_id', $user->id)
                ->whereDate(
                    'created_at',
                    Carbon::today()
                )
                ->exists();

            if (! $alreadyViewedToday) {
                ProfileView::create([
                    'viewer_id' => Auth::id(),
                    'viewed_id' => $user->id,
                ]);
            }
        }

        return view(
            'profile.show',
            compact('user')
        );
    }

    /**
     * جست‌وجوی کاربران.
     */
    public function search(Request $request)
    {
        $query = User::query();

        if ($request->filled('city')) {
            $query->where(
                'city',
                'like',
                '%' . $request->input('city') . '%'
            );
        }

        if (
            $request->filled('min_age') ||
            $request->filled('max_age')
        ) {
            $minAge = (int) $request->input(
                'min_age',
                18
            );

            $maxAge = (int) $request->input(
                'max_age',
                99
            );

            $currentYear = Carbon::now()->year;

            $minBirthYear =
                $currentYear - $maxAge;

            $maxBirthYear =
                $currentYear - $minAge;

            $query->whereBetween('birth_year', [
                $minBirthYear,
                $maxBirthYear,
            ]);
        }

        if ($request->filled('marital_status')) {
            $query->where(
                'marital_status',
                $request->input('marital_status')
            );
        }

        if ($request->filled('interested_in')) {
            $query->where(
                'interests',
                'like',
                '%' .
                $request->input('interested_in') .
                '%'
            );
        }

        if ($request->boolean('has_photo')) {
            $query->whereNotNull(
                'profile_picture'
            );
        }

        if ($request->boolean('is_active')) {
            $query->where(
                'is_active',
                true
            );
        }

        $profiles = $query
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view(
            'search',
            compact('profiles')
        );
    }
}