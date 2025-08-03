@extends('layouts.app')

@section('content')
    <div class="max-w-3xl mx-auto mt-10 p-6 bg-white shadow-md rounded-lg">
        <h2 class="text-2xl font-semibold mb-6 text-center">ثبت‌نام</h2>
        <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data">
            @csrf

            <!-- Name -->
            <div class="mb-4">
                <label for="name" class="block mb-1 font-medium">نام</label>
                <input type="text" id="name" name="name"
                    class="w-full border border-gray-300 rounded px-3 py-2 @error('name') border-red-500 @enderror"
                    value="{{ old('name') }}" required autocomplete="name">
                @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email -->
            <div class="mb-4">
                <label for="email" class="block mb-1 font-medium">ایمیل</label>
                <input type="email" id="email" name="email"
                    class="w-full border border-gray-300 rounded px-3 py-2 @error('email') border-red-500 @enderror"
                    value="{{ old('email') }}" required autocomplete="email">
                @error('email')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Gender -->
            <div class="mb-4">
                <label for="gender" class="block mb-1 font-medium">جنسیت</label>
                <select id="gender" name="gender"
                    class="w-full border border-gray-300 rounded px-3 py-2 @error('gender') border-red-500 @enderror">
                    <option value="male">مرد</option>
                    <option value="female">زن</option>
                </select>
                @error('gender')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Age -->
            <div class="mb-4">
                <label for="age" class="block mb-1 font-medium">سن</label>
                <input type="number" id="age" name="age"
                    class="w-full border border-gray-300 rounded px-3 py-2 @error('age') border-red-500 @enderror"
                    value="{{ old('age') }}" required>
                @error('age')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- City -->
            <div class="mb-4">
                <label for="city" class="block mb-1 font-medium">شهر</label>
                <input type="text" id="city" name="city"
                    class="w-full border border-gray-300 rounded px-3 py-2 @error('city') border-red-500 @enderror"
                    value="{{ old('city') }}" required>
                @error('city')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Bio -->
            <div class="mb-4">
                <label for="bio" class="block mb-1 font-medium">بیوگرافی</label>
                <textarea id="bio" name="bio" rows="3"
                    class="w-full border border-gray-300 rounded px-3 py-2 @error('bio') border-red-500 @enderror" required>{{ old('bio') }}</textarea>
                @error('bio')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Interested In -->
            <div class="mb-4">
                <label for="interested_in" class="block mb-1 font-medium">علاقه‌مندی‌ها</label>
                <select id="interested_in" name="interested_in"
                    class="w-full border border-gray-300 rounded px-3 py-2 @error('interested_in') border-red-500 @enderror">
                    <option value="ورزش">ورزش</option>
                    <option value="مسافرت">مسافرت</option>
                    <option value="کتاب">کتاب</option>
                    <option value="مهمانی">مهمانی</option>
                </select>
                @error('interested_in')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Salary -->
            <div class="mb-4">
                <label for="salary" class="block mb-1 font-medium">درآمد ماهانه</label>
                <input type="number" id="salary" name="salary" min="0"
                    class="w-full border border-gray-300 rounded px-3 py-2 @error('salary') border-red-500 @enderror"
                    value="{{ old('salary') }}">
                @error('salary')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Profile Picture -->
            <div class="mb-4 flex flex-col sm:flex-row items-start sm:items-center">
                <label for="profile_picture" class="w-full sm:w-1/3 text-sm font-medium text-gray-700 mb-2 sm:mb-0">
                    انتخاب عکس پروفایل:
                </label>
                <div class="w-full sm:w-2/3">
                    <input type="file" id="profile_picture" name="profile_picture" accept="image/*"
                        class="block w-full text-sm text-gray-700 file:mr-4 file:py-2 file:px-4
                   file:rounded-md file:border-0
                   file:text-sm file:font-semibold
                   file:bg-blue-50 file:text-blue-700
                   hover:file:bg-blue-100" />
                    @error('profile_picture')
                        <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Password -->
            <div class="mb-4">
                <label for="password" class="block mb-1 font-medium">رمز عبور</label>
                <input type="password" id="password" name="password"
                    class="w-full border border-gray-300 rounded px-3 py-2 @error('password') border-red-500 @enderror"
                    required autocomplete="new-password">
                @error('password')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password Confirm -->
            <div class="mb-6">
                <label for="password-confirm" class="block mb-1 font-medium">تکرار رمز عبور</label>
                <input type="password" id="password-confirm" name="password_confirmation"
                    class="w-full border border-gray-300 rounded px-3 py-2" required autocomplete="new-password">
            </div>

            <div class="flex justify-end">
                <button type="submit" class="bg-blue-600 text-white px-5 py-2 rounded hover:bg-blue-700 transition">
                    ثبت‌نام
                </button>
            </div>
        </form>
    </div>
@endsection
