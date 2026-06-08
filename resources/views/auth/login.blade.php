@extends('layouts.app')

@section('content')
<div class="flex items-center justify-center min-h-screen bg-pink-50/30 dark:bg-slate-950 transition-colors duration-300 px-0 sm:px-4">
    <div class="w-full h-screen sm:h-auto sm:max-w-md px-6 py-10 bg-white dark:bg-slate-900 sm:rounded-3xl sm:shadow-2xl border-0 sm:border border-pink-100/50 dark:border-slate-800 flex flex-col justify-center sm:block transition-all duration-300">
        
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-gradient-to-tr from-pink-500 to-rose-500 text-white shadow-lg shadow-pink-200 dark:shadow-none mb-4 transform rotate-12">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 transform -rotate-12" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                </svg>
            </div>
            <h2 class="text-3xl font-extrabold text-gray-800 dark:text-slate-100 tracking-tight">{{ __('auth.login_title') }}</h2>
            <p class="text-sm text-gray-500 dark:text-slate-400 mt-2">خوش آمدید! برای پیدا کردن زوج مناسب خود وارد شوید.</p>
        </div>

        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

            <div>
                <label for="email" class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-1.5">{{ __('auth.email') }}</label>
                <div class="relative">
                    <input id="email" type="email" name="email"
                        class="block w-full rounded-2xl border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-slate-800/50 text-gray-800 dark:text-slate-200 p-3.5 focus:border-pink-500 focus:ring focus:ring-pink-200 dark:focus:ring-pink-900/30 focus:ring-opacity-50 outline-none transition-all @error('email') border-red-500 dark:border-red-500 focus:ring-red-200 @enderror"
                        value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="name@example.com">
                </div>
                @error('email')
                    <p class="mt-1.5 text-xs font-medium text-red-600 dark:text-red-400 flex items-center gap-1">
                        <span>⚠️</span> {{ $message }}
                    </p>
                @enderror
            </div>

            <div>
                <div class="flex justify-between items-center mb-1.5">
                    <label for="password" class="block text-sm font-bold text-gray-700 dark:text-slate-300">{{ __('auth.password') }}</label>
                </div>
                <input id="password" type="password" name="password"
                    class="block w-full rounded-2xl border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-slate-800/50 text-gray-800 dark:text-slate-200 p-3.5 focus:border-pink-500 focus:ring focus:ring-pink-200 dark:focus:ring-pink-900/30 focus:ring-opacity-50 outline-none transition-all @error('password') border-red-500 dark:border-red-500 focus:ring-red-200 @enderror"
                    required autocomplete="current-password" placeholder="••••••••">
                @error('password')
                    <p class="mt-1.5 text-xs font-medium text-red-600 dark:text-red-400 flex items-center gap-1">
                        <span>⚠️</span> {{ $message }}
                    </p>
                @enderror
            </div>

            <div class="flex items-center justify-between pt-1">
                <label class="relative flex items-center cursor-pointer select-none">
                    <input id="remember" type="checkbox" name="remember"
                        class="w-5 h-5 text-pink-600 border-gray-300 dark:border-slate-700 rounded-lg focus:ring-pink-500 focus:ring-offset-0 dark:bg-slate-800"
                        {{ old('remember') ? 'checked' : '' }}>
                    <span class="mr-2 text-sm text-gray-600 dark:text-slate-400 font-medium">{{ __('auth.remember_me') }}</span>
                </label>

                @if (Route::has('password.request'))
                    <a class="text-sm font-semibold text-pink-500 dark:text-pink-400 hover:text-pink-600 dark:hover:text-pink-300 hover:underline transition duration-200"
                        href="{{ route('password.request') }}">
                        {{ __('auth.forgot_password') }}
                    </a>
                @endif
            </div>

            <div class="pt-2">
                <button type="submit"
                    class="w-full py-3.5 px-4 text-white font-bold bg-gradient-to-r from-pink-500 to-rose-500 hover:from-pink-600 hover:to-rose-600 rounded-2xl shadow-xl shadow-pink-100 dark:shadow-none hover:shadow-none transition duration-300 transform active:scale-[0.98] outline-none text-base">
                    {{ __('auth.login_button') }}
                </button>
            </div>
        </form>

        @if (Route::has('register'))
            <div class="mt-8 text-center border-t border-gray-100 dark:border-slate-800/80 pt-6">
                <p class="text-sm text-gray-500 dark:text-slate-400">
                    هنوز عضو نشده‌اید؟
                    <a href="{{ route('register') }}" class="font-bold text-pink-500 dark:text-pink-400 hover:text-pink-600 dark:hover:text-pink-300 transition duration-200">
                        ثبت نام رایگان
                    </a>
                </p>
            </div>
        @endif
        
    </div>
</div>
@endsection