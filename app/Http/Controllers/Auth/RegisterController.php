<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Rules\SafeProfilePhotoDimensions;
use App\Services\ProfilePhotoService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

class RegisterController extends Controller
{
    use RegistersUsers;

    /**
     * مسیر انتقال بعد از ثبت‌نام.
     *
     * کاربر در متد register مستقیماً به صفحه تأیید
     * ایمیل منتقل می‌شود.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * فقط کاربران مهمان به ثبت‌نام دسترسی دارند.
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * ثبت‌نام کاربر جدید.
     */
    public function register(
        Request $request,
        ProfilePhotoService $photos
    ) {
        $validatedData = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users,email',
            ],

            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],

            'age' => [
                'required',
                'integer',
                'min:18',
                'max:100',
            ],

            'gender' => [
                'required',
                'in:male,female,other',
            ],

            'bio' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'city' => [
                'required',
                'string',
                'max:255',
            ],

            'interested_in' => [
                'required',
                'string',
                'max:100',
            ],

            'salary' => [
                'nullable',
                'integer',
                'min:0',
            ],

            'salary_visible' => ['nullable', 'boolean'],

            'marital_status' => [
                'nullable',
                'in:single,married,divorced,widowed',
            ],

            'profile_picture' => [
                'nullable',
                'bail',
                'max:2048',
                'image',
                'mimes:jpeg,png,jpg,gif',
                'dimensions:max_width=4096,max_height=4096',
                new SafeProfilePhotoDimensions(),
            ],

            'cf-turnstile-response' => [
                'required',
                'string',
                'max:2048',
            ],
        ], [
            'name.required' => 'واردکردن نام الزامی است.',
            'name.string' => 'نام باید به‌صورت متن وارد شود.',
            'name.max' => 'نام نباید بیشتر از ۲۵۵ نویسه باشد.',
            'email.required' => 'واردکردن نشانی ایمیل الزامی است.',
            'email.email' => 'قالب ایمیل صحیح نیست؛ نمونه صحیح: name@example.com',
            'email.max' => 'ایمیل نباید بیشتر از ۲۵۵ نویسه باشد.',
            'email.unique' => 'این ایمیل قبلاً ثبت شده است؛ وارد حساب شوید یا رمز عبور را بازیابی کنید.',
            'password.required' => 'واردکردن رمز عبور الزامی است.',
            'password.min' => 'رمز عبور باید حداقل ۸ نویسه داشته باشد.',
            'password.confirmed' => 'رمز عبور و تکرار آن یکسان نیستند.',
            'age.required' => 'واردکردن سن الزامی است.',
            'age.integer' => 'سن باید به‌صورت عدد صحیح وارد شود.',
            'age.min' => 'برای ثبت‌نام باید حداقل ۱۸ سال داشته باشید.',
            'age.max' => 'سن واردشده نمی‌تواند بیشتر از ۱۰۰ سال باشد.',
            'gender.required' => 'انتخاب جنسیت الزامی است.',
            'gender.in' => 'گزینه انتخاب‌شده برای جنسیت معتبر نیست.',
            'bio.max' => 'متن معرفی نباید بیشتر از ۱۰۰۰ نویسه باشد.',
            'city.required' => 'واردکردن شهر محل سکونت الزامی است.',
            'city.max' => 'نام شهر نباید بیشتر از ۲۵۵ نویسه باشد.',
            'interested_in.required' => 'انتخاب علاقه‌مندی الزامی است.',
            'interested_in.max' => 'مقدار علاقه‌مندی بیش از حد طولانی است.',
            'salary.integer' => 'درآمد ماهانه باید به‌صورت عدد صحیح وارد شود.',
            'salary.min' => 'درآمد ماهانه نمی‌تواند منفی باشد.',
            'salary_visible.boolean' => 'وضعیت نمایش درآمد معتبر نیست.',
            'marital_status.in' => 'گزینه وضعیت تأهل معتبر نیست.',
            'profile_picture.max' => 'حجم تصویر پروفایل نباید بیشتر از ۲ مگابایت باشد.',
            'profile_picture.image' => 'فایل انتخاب‌شده تصویر معتبر نیست.',
            'profile_picture.mimes' => 'فرمت تصویر باید JPG، JPEG، PNG یا GIF باشد.',
            'profile_picture.dimensions' => 'طول یا عرض تصویر نباید بیشتر از ۴۰۹۶ پیکسل باشد.',
            'profile_picture.uploaded' => 'بارگذاری تصویر کامل نشد؛ حجم فایل و اتصال را بررسی کنید.',
            'cf-turnstile-response.required' => 'تأیید امنیتی انجام نشده است؛ کپچا را کامل کنید.',
            'cf-turnstile-response.string' => 'پاسخ امنیتی معتبر نیست؛ صفحه را تازه‌سازی کنید.',
            'cf-turnstile-response.max' => 'پاسخ امنیتی نامعتبر است؛ دوباره تلاش کنید.',
        ], [
            'name' => 'نام',
            'email' => 'ایمیل',
            'password' => 'رمز عبور',
            'age' => 'سن',
            'gender' => 'جنسیت',
            'bio' => 'معرفی کوتاه',
            'city' => 'شهر',
            'interested_in' => 'علاقه‌مندی',
            'salary' => 'درآمد ماهانه',
            'salary_visible' => 'نمایش درآمد',
            'marital_status' => 'وضعیت تأهل',
            'profile_picture' => 'تصویر پروفایل',
            'cf-turnstile-response' => 'تأیید امنیتی',
        ]);

        /*
         * بررسی CAPTCHA در سمت سرور.
         *
         * صرف نمایش CAPTCHA در مرورگر کافی نیست.
         */
        $this->validateTurnstile($request);

        $currentYear = (int) date('Y');
        $birthYear = $currentYear - $validatedData['age'];

        $picturePath = null;

        if ($request->hasFile('profile_picture')) {
            $picturePath = $photos->store(
                $request->file('profile_picture')
            );

            $validatedData['profile_picture'] =
                $picturePath;
        }

        try {
            $user = User::create([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'password' => Hash::make(
                    $validatedData['password']
                ),
                'gender' => $validatedData['gender'],
                'age' => $validatedData['age'],
                'birth_year' => $birthYear,
                'city' => $validatedData['city'],
                'interested_in' =>
                    $validatedData['interested_in'],
                'salary' => $validatedData['salary'] ?? null,
                'salary_visible' => $request->boolean('salary_visible'),
                'marital_status' =>
                    $validatedData['marital_status'] ?? null,
                'bio' => $validatedData['bio'] ?? null,
                'profile_picture' =>
                    $validatedData['profile_picture'] ?? null,
            ]);
        } catch (\Throwable $exception) {
            if ($picturePath !== null) {
                $photos->delete($picturePath);
            }

            throw $exception;
        }

        /*
         * این رویداد باعث ارسال ایمیل تأیید می‌شود.
         */
        event(new Registered($user));

        /*
         * کاربر فقط برای دسترسی به صفحه تأیید و
         * ارسال مجدد ایمیل وارد Session می‌شود.
         *
         * middleware verified اجازه دسترسی به
         * قسمت‌های اصلی سایت را نمی‌دهد.
         */
        Auth::login($user);

        return redirect()->route('verification.notice');
    }

    /**
     * بررسی توکن Cloudflare Turnstile.
     */
    private function validateTurnstile(
        Request $request
    ): void {
        $secretKey = (string) config(
            'services.turnstile.secret_key'
        );

        $verifyUrl = (string) config(
            'services.turnstile.verify_url'
        );

        if ($secretKey === '' || $verifyUrl === '') {
            throw ValidationException::withMessages([
                'cf-turnstile-response' =>
                    'سامانه ضدربات پیکربندی نشده است. لطفاً با پشتیبانی تماس بگیرید.',
            ]);
        }

        try {
            $response = Http::asForm()
                ->timeout(10)
                ->post($verifyUrl, [
                    'secret' => $secretKey,
                    'response' => $request->input(
                        'cf-turnstile-response'
                    ),
                    'remoteip' => $request->ip(),
                ]);
        } catch (\Throwable $exception) {
            /*
             * خطای فنی در لاگ ثبت می‌شود، ولی
             * اطلاعات حساس به کاربر نمایش داده نمی‌شود.
             */
            report($exception);

            throw ValidationException::withMessages([
                'cf-turnstile-response' =>
                    'ارتباط با سامانه ضدربات برقرار نشد. لطفاً دوباره تلاش کنید.',
            ]);
        }

        /*
         * Official Turnstile test keys return a successful response marked
         * with result_with_testing_key, but may omit the widget action.
         * Accept that response only outside production. Real production
         * responses must still contain the expected register action.
         */
        $isOfficialTestResponse =
            app()->environment(['local', 'testing']) &&
            $response->json('metadata.result_with_testing_key') === true;

        $hasExpectedAction = $response->json('action') === 'register';

        if (
            ! $response->successful() ||
            $response->json('success') !== true ||
            (! $hasExpectedAction && ! $isOfficialTestResponse)
        ) {
            throw ValidationException::withMessages([
                'cf-turnstile-response' =>
                    'تأیید ضدربات ناموفق بود. لطفاً دوباره تلاش کنید.',
            ]);
        }
    }
}
