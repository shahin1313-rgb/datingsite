@extends('layouts.app')

@section('content')
    <div class="container mx-auto max-w-2xl py-10 px-4" dir="{{ app()->getLocale() === 'fa' ? 'rtl' : 'ltr' }}">
        <div class="bg-white shadow-lg rounded-3xl overflow-hidden">
            
            <!-- Header -->
            <div class="relative bg-gradient-to-tr from-indigo-500 via-blue-500 to-sky-400 h-40">
                <div class="absolute -bottom-12 right-6 flex items-center">
                    <div class="relative">
                        <img src="{{ asset('storage/' . $user->profile_picture) }}?v={{ time() }}"
                            class="w-24 h-24 rounded-full border-4 border-white shadow-md object-cover" alt="Profile Picture">
                    </div>
                </div>
            </div>

            <!-- Info -->
            <div class="pt-16 text-center px-6 pb-4">
                <h2 class="text-xl font-semibold text-gray-800">{{ $user->name }}</h2>
                <p class="text-sm text-gray-500 mt-1">{{ __('profile.designer') }}</p>
            </div>

            <!-- Form -->
            <div class="px-6 pb-8">
                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                    @csrf

                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-600 mb-1">{{ __('profile.name') }}</label>
                        <input type="text" id="name" name="name" value="{{ $user->name }}"
                            class="w-full border border-gray-300 rounded-xl px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none transition" required>
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-600 mb-1">{{ __('profile.email') }}</label>
                        <input type="email" id="email" name="email" value="{{ $user->email }}"
                            class="w-full border border-gray-300 rounded-xl px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none transition" required>
                    </div>

                    <div>
                        <label for="city" class="block text-sm font-medium text-gray-600 mb-1">{{ __('profile.city') }}</label>
                        <input type="text" id="city" name="city" value="{{ $user->city }}"
                            class="w-full border border-gray-300 rounded-xl px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none transition">
                    </div>

                    <div class="mt-4">
                        <label for="marital_status" class="block text-sm font-medium text-gray-700">{{ __('profile.marital_status') }}</label>
                        <select id="marital_status" name="marital_status"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            <option value="">{{ __('profile.select') }}</option>
                            <option value="single" {{ old('marital_status', $user->marital_status) == 'single' ? 'selected' : '' }}>{{ __('profile.single') }}</option>
                            <option value="married" {{ old('marital_status', $user->marital_status) == 'married' ? 'selected' : '' }}>{{ __('profile.married') }}</option>
                            <option value="divorced" {{ old('marital_status', $user->marital_status) == 'divorced' ? 'selected' : '' }}>{{ __('profile.divorced') }}</option>
                            <option value="widowed" {{ old('marital_status', $user->marital_status) == 'widowed' ? 'selected' : '' }}>{{ __('profile.widowed') }}</option>
                        </select>
                    </div>

                    <div>
                        <label for="bio" class="block text-sm font-medium text-gray-600 mb-1">{{ __('profile.bio') }}</label>
                        <textarea id="bio" name="bio" rows="4"
                            class="w-full border border-gray-300 rounded-xl px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none transition">{{ $user->bio }}</textarea>
                    </div>

                    <div class="relative">
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('profile.profile_picture') }}</label>
                        <img id="preview" src="{{ asset('storage/' . $user->profile_picture) }}" alt="Profile Picture"
                            class="w-24 h-24 rounded-full border-2 border-gray-300 mt-2 object-cover">
                        <input type="file" id="profile_picture" name="profile_picture" accept="image/*"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" onchange="previewImage(event)">
                    </div>

                    <div class="flex justify-end">
                        <button type="submit"
                            class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium px-6 py-2 rounded-xl transition duration-200">
                            {{ __('profile.save') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function previewImage(event) {
            const reader = new FileReader();
            reader.onload = function() {
                const output = document.getElementById('preview');
                output.src = reader.result;
            }
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>
@endsection
