<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
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
    public function register(Request $request)
    {
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
                'required',
                'integer',
                'min:0',
            ],

            'marital_status' => [
                'nullable',
                'in:single,married,divorced,widowed',
            ],

            'profile_picture' => [
                'nullable',
                'image',
                'mimes:jpeg,png,jpg,gif',
                'max:2048',
            ],

            'cf-turnstile-response' => [
                'required',
                'string',
                'max:2048',
            ],
        ]);

        /*
         * بررسی CAPTCHA در سمت سرور.
         *
         * صرف نمایش CAPTCHA در مرورگر کافی نیست.
         */
        $this->validateTurnstile($request);

        $currentYear = (int) date('Y');
        $birthYear = $currentYear - $validatedData['age'];

        if ($request->hasFile('profile_picture')) {
            $path = $request
                ->file('profile_picture')
                ->store('profile_pictures', 'public');

            $validatedData['profile_picture'] = $path;
        }

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
            'salary' => $validatedData['salary'],
            'marital_status' =>
                $validatedData['marital_status'] ?? null,
            'bio' => $validatedData['bio'] ?? null,
            'profile_picture' =>
                $validatedData['profile_picture'] ?? null,
        ]);

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

        if (
            ! $response->successful() ||
            $response->json('success') !== true ||
            $response->json('action') !== 'register'
        ) {
            throw ValidationException::withMessages([
                'cf-turnstile-response' =>
                    'تأیید ضدربات ناموفق بود. لطفاً دوباره تلاش کنید.',
            ]);
        }
    }
}