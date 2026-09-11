@extends('layouts.app')
@section('content')
<div class="min-h-[70vh] flex items-center justify-center px-4"><div class="w-full max-w-md bg-white rounded-3xl shadow-xl border border-pink-100 p-8">
    <h1 class="text-2xl font-extrabold text-gray-800 mb-2">تأیید رمز عبور</h1><p class="text-sm text-gray-500 mb-6">برای ادامه، رمز عبور خود را دوباره وارد کنید.</p>
    <form method="POST" action="{{ route('password.confirm') }}" class="space-y-5">@csrf<div><label for="password" class="block text-sm font-bold text-gray-700 mb-2">رمز عبور</label><input id="password" type="password" name="password" required autocomplete="current-password" class="w-full rounded-2xl border-gray-200 focus:border-pink-500 focus:ring-pink-500">@error('password')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror</div><button type="submit" class="w-full rounded-2xl bg-pink-600 hover:bg-pink-700 text-white font-bold py-3">تأیید</button></form>
</div></div>
@endsection
