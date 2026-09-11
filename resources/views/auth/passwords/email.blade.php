@extends('layouts.app')
@section('content')
<div class="min-h-[70vh] flex items-center justify-center px-4"><div class="w-full max-w-md bg-white rounded-3xl shadow-xl border border-pink-100 p-8">
    <h1 class="text-2xl font-extrabold text-gray-800 mb-2">بازیابی رمز عبور</h1><p class="text-sm text-gray-500 mb-6">ایمیل حساب خود را وارد کنید.</p>
    @if(session('status'))<div class="mb-4 rounded-xl bg-green-50 p-4 text-sm text-green-700" role="status">{{ session('status') }}</div>@endif
    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">@csrf
        <div><label for="email" class="block text-sm font-bold text-gray-700 mb-2">ایمیل</label><input id="email" type="email" name="email" value="{{ old('email') }}" autocomplete="email" required autofocus class="w-full rounded-2xl border-gray-200 focus:border-pink-500 focus:ring-pink-500">@error('email')<p class="mt-2 text-sm text-red-600" role="alert">{{ $message }}</p>@enderror</div>
        <button type="submit" class="w-full rounded-2xl bg-pink-600 hover:bg-pink-700 text-white font-bold py-3">ارسال لینک بازیابی</button>
    </form>
</div></div>
@endsection
