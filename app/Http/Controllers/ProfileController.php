<?php

namespace App\Http\Controllers;

use App\Models\ProfileView;
use App\Models\User;
use App\Services\ProfilePhotoService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

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
    public function update(
        Request $request,
        ProfilePhotoService $photos
    ) {
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

            /*
             * رمز فعلی فقط زمانی اجباری است که آدرس ایمیل
             * واقعاً تغییر کرده باشد.
             */
            'current_password' => [
                Rule::requiredIf(
                    fn (): bool =>
                        $request->input('email') !==
                        $user->email
                ),
                'nullable',
                'string',
                'current_password',
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
                'dimensions:max_width=4096,max_height=4096',
                'max:2048',
            ],
        ], [
            'current_password.required' =>
                'برای تغییر ایمیل، رمز عبور فعلی خود را وارد کنید.',

            'current_password.current_password' =>
                'رمز عبور فعلی صحیح نیست.',
        ]);

        /*
         * این فیلد فقط برای تأیید هویت است و نباید
         * در مدل User ذخیره شود.
         */
        unset($validated['current_password']);

        $oldPicturePath = $user->profile_picture;
        $newPicturePath = null;

        if ($request->hasFile('profile_picture')) {
            $newPicturePath = $photos->store(
                $request->file('profile_picture')
            );

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

        try {
            $user->save();
        } catch (\Throwable $exception) {
            if ($newPicturePath !== null) {
                $photos->delete($newPicturePath);
            }

            throw $exception;
        }

        /*
         * The old file is removed only after the database points to the
         * successfully stored private replacement.
         */
        if ($newPicturePath !== null) {
            $photos->delete($oldPicturePath);
        }

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
