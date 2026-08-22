<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * آدرس انتقال کاربر پس از ورود موفق.
     */
    protected $redirectTo = '/home';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    /**
     * پس از درست بودن ایمیل و رمز عبور اجرا می‌شود.
     */
    protected function authenticated(Request $request, $user)
    {
        if ((bool) $user->banned) {
            Auth::logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->route('login')
                ->withInput($request->only('email'))
                ->withErrors([
                    'email' => 'حساب کاربری شما توسط مدیریت مسدود شده است.',
                ]);
        }

        // فقط ورود موفق کاربر آزادشده ثبت شود
        $user->last_login_at = now();
        $user->save();

        return redirect()->intended($this->redirectPath());
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}