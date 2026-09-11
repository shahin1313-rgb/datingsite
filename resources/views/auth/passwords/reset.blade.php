@extends('layouts.app')
@section('content')
<div class="min-h-[70vh] flex items-center justify-center px-4"><div class="w-full max-w-md bg-white rounded-3xl shadow-xl border border-pink-100 p-8">
    <h1 class="text-2xl font-extrabold text-gray-800 mb-6">تنظیم رمز عبور جدید</h1>
    <form method="POST" action="{{ route('password.update') }}" class="space-y-5">@csrf<input type="hidden" name="token" value="{{ $token }}">
        <div><label for="email" class="block text-sm font-bold text-gray-700 mb-2">ایمیل</label><input id="email" type="email" name="email" value="{{ old('email', $email ?? '') }}" required autocomplete="email" class="w-full rounded-2xl border-gray-200 focus:border-pink-500 focus:ring-pink-500">@error('email')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror</div>
        <div><label for="password" class="block text-sm font-bold text-gray-700 mb-2">رمز عبور جدید</label><input id="password" type="password" name="password" required autocomplete="new-password" class="w-full rounded-2xl border-gray-200 focus:border-pink-500 focus:ring-pink-500">@error('password')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror</div>
        <div><label for="password_confirmation" class="block text-sm font-bold text-gray-700 mb-2">تکرار رمز عبور</label><input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" class="w-full rounded-2xl border-gray-200 focus:border-pink-500 focus:ring-pink-500"></div>
        <button type="submit" class="w-full rounded-2xl bg-pink-600 hover:bg-pink-700 text-white font-bold py-3">ثبت رمز عبور</button>
    </form>
</div></div>
@endsection
