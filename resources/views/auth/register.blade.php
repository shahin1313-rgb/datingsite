@extends('layouts.app')

@section('content')
    <div class="max-w-3xl mx-auto mt-10 p-6 bg-white shadow-md rounded-lg">
        <h2 class="text-2xl font-semibold mb-6 text-center">
            {{ __('Registerpage.register') }}
        </h2>

        <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data">
            @csrf

            <!-- Name -->
            <div class="mb-4">
                <label for="name" class="block mb-1 font-medium">{{ __('Registerpage.name') }}</label>
                <input type="text" id="name" name="name"
                       class="w-full border border-gray-300 rounded px-3 py-2"
                       value="{{ old('name') }}" required>
            </div>

            <!-- Email -->
            <div class="mb-4">
                <label for="email" class="block mb-1 font-medium">{{ __('Registerpage.email') }}</label>
                <input type="email" id="email" name="email"
                       class="w-full border border-gray-300 rounded px-3 py-2"
                       value="{{ old('email') }}" required>
            </div>

            <!-- Gender -->
            <div class="mb-4">
                <label for="gender" class="block mb-1 font-medium">{{ __('Registerpage.gender') }}</label>
                <select id="gender" name="gender" class="w-full border border-gray-300 rounded px-3 py-2">
                    <option value="male">{{ __('Registerpage.male') }}</option>
                    <option value="female">{{ __('Registerpage.female') }}</option>
                </select>
            </div>

            <!-- Age -->
            <div class="mb-4">
                <label for="age" class="block mb-1 font-medium">{{ __('Registerpage.age') }}</label>
                <input type="number" id="age" name="age"
                       class="w-full border border-gray-300 rounded px-3 py-2"
                       value="{{ old('age') }}" required>
            </div>

            <!-- City -->
            <div class="mb-4">
                <label for="city" class="block mb-1 font-medium">{{ __('Registerpage.city') }}</label>
                <input type="text" id="city" name="city"
                       class="w-full border border-gray-300 rounded px-3 py-2"
                       value="{{ old('city') }}" required>
            </div>

            <!-- Marital Status -->
            <div class="mb-4">
                <label for="marital_status" class="block mb-1 font-medium">{{ __('Registerpage.marital_status') }}</label>
                <select id="marital_status" name="marital_status"
                        class="w-full border border-gray-300 rounded px-3 py-2">
                    <option value="">{{ __('Registerpage.select') }}</option>
                    <option value="single">{{ __('Registerpage.single') }}</option>
                    <option value="married">{{ __('Registerpage.married') }}</option>
                    <option value="divorced">{{ __('Registerpage.divorced') }}</option>
                    <option value="widowed">{{ __('Registerpage.widowed') }}</option>
                </select>
            </div>

            <!-- Bio -->
            <div class="mb-4">
                <label for="bio" class="block mb-1 font-medium">{{ __('Registerpage.bio') }}</label>
                <textarea id="bio" name="bio" rows="3"
                          class="w-full border border-gray-300 rounded px-3 py-2">{{ old('bio') }}</textarea>
            </div>

            <!-- Interested In -->
            <div class="mb-4">
                <label for="interested_in" class="block mb-1 font-medium">{{ __('Registerpage.interested_in') }}</label>
                <select id="interested_in" name="interested_in"
                        class="w-full border border-gray-300 rounded px-3 py-2">
                    <option value="sport">ورزش / Sport</option>
                    <option value="travel">مسافرت / Voyage</option>
                    <option value="books">کتاب / Livre</option>
                    <option value="party">مهمانی / Fête</option>
                </select>
            </div>

            <!-- Salary -->
            <div class="mb-4">
                <label for="salary" class="block mb-1 font-medium">{{ __('Registerpage.salary') }}</label>
                <input type="number" id="salary" name="salary" min="0"
                       class="w-full border border-gray-300 rounded px-3 py-2"
                       value="{{ old('salary') }}">
            </div>

            <!-- Profile Picture -->
            <div class="mb-4">
                <label for="profile_picture" class="block mb-1 font-medium">{{ __('Registerpage.profile_picture') }}</label>
                <input type="file" id="profile_picture" name="profile_picture" accept="image/*"
                       class="w-full text-sm">
            </div>

            <!-- Password -->
            <div class="mb-4">
                <label for="password" class="block mb-1 font-medium">{{ __('Registerpage.password') }}</label>
                <input type="password" id="password" name="password"
                       class="w-full border border-gray-300 rounded px-3 py-2" required>
            </div>

            <!-- Password Confirm -->
            <div class="mb-6">
                <label for="password-confirm" class="block mb-1 font-medium">{{ __('Registerpage.password_confirm') }}</label>
                <input type="password" id="password-confirm" name="password_confirmation"
                       class="w-full border border-gray-300 rounded px-3 py-2" required>
            </div>

            <div class="flex justify-end">
                <button type="submit"
                        class="bg-blue-600 text-white px-5 py-2 rounded hover:bg-blue-700 transition">
                    {{ __('Registerpage.submit') }}
                </button>
            </div>
        </form>
    </div>
@endsection
