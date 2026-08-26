@extends('layouts.app')

@section('content')
    <div
        class="container mx-auto max-w-2xl py-10 px-4"
        dir="{{ app()->getLocale() === 'fa' ? 'rtl' : 'ltr' }}"
    >
        <div
            class="bg-white/80 backdrop-blur-md shadow-2xl rounded-[2.5rem] overflow-hidden border border-pink-100"
        >
            <div
                class="relative bg-gradient-to-tr from-rose-400 via-fuchsia-500 to-pink-400 h-48"
            >
                <div
                    class="absolute -bottom-16 inset-x-0 flex justify-center"
                >
                    <div class="relative">
                        <img
                            id="header-avatar"
                            src="{{ $user->profilePhotoUrl() }}?v={{ time() }}"
                            class="w-32 h-32 rounded-full border-4 border-white shadow-xl object-cover ring-4 ring-pink-50/50"
                            alt="Profile Picture"
                        >

                        <div
                            class="absolute bottom-1 right-1 bg-white p-1.5 rounded-full shadow-md text-pink-500"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M4 5a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V7a2 2 0 00-2-2h-1.586a1 1 0 01-.707-.293l-1.113-1.113A2 2 0 0010.192 3H9.808a2 2 0 00-1.407.586L7.288 4.707A1 1 0 016.586 5H4zm6 9a3 3 0 100-6 3 3 0 000 6z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <div class="pt-20 text-center px-6 pb-4">
                <h2 class="text-2xl font-bold text-gray-800">
                    {{ $user->name }}
                </h2>

                <p
                    class="text-pink-500 font-medium flex items-center justify-center gap-1 mt-1"
                >
                    <span
                        class="w-2 h-2 bg-green-400 rounded-full animate-pulse"
                    ></span>

                    {{ __('profile.designer') }}
                </p>
            </div>

            <div class="px-8 pb-10">
                <form
                    action="{{ route('profile.update') }}"
                    method="POST"
                    enctype="multipart/form-data"
                    class="space-y-6"
                >
                    @csrf

                    <div
                        class="grid grid-cols-1 md:grid-cols-2 gap-6"
                    >
                        <div>
                            <label
                                for="name"
                                class="block text-sm font-semibold text-gray-600 mb-2 px-1"
                            >
                                {{ __('profile.name') }}
                            </label>

                            <input
                                type="text"
                                id="name"
                                name="name"
                                value="{{ old('name', $user->name) }}"
                                class="w-full bg-pink-50/50 border-0 rounded-2xl px-4 py-3 focus:ring-2 focus:ring-pink-400 focus:bg-white transition-all outline-none"
                                required
                            >
                        </div>

                        <div>
                            <label
                                for="email"
                                class="block text-sm font-semibold text-gray-600 mb-2 px-1"
                            >
                                {{ __('profile.email') }}
                            </label>

                            <input
                                type="email"
                                id="email"
                                name="email"
                                value="{{ old('email', $user->email) }}"
                                class="w-full bg-pink-50/50 border-0 rounded-2xl px-4 py-3 focus:ring-2 focus:ring-pink-400 focus:bg-white transition-all outline-none"
                                required
                            >

                            @error('email')
                                <p
                                    class="mt-2 rounded-xl bg-red-50 p-3 text-sm font-semibold text-red-600"
                                    role="alert"
                                >
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label
                            for="current_password"
                            class="block text-sm font-semibold text-gray-600 mb-2 px-1"
                        >
                            {{ __('profile.current_password') }}
                        </label>

                        <input
                            type="password"
                            id="current_password"
                            name="current_password"
                            autocomplete="current-password"
                            aria-describedby="current-password-help"
                            class="w-full bg-pink-50/50 border-0 rounded-2xl px-4 py-3 focus:ring-2 focus:ring-pink-400 focus:bg-white transition-all outline-none"
                        >

                        <p
                            id="current-password-help"
                            class="mt-2 px-1 text-xs leading-6 text-gray-500"
                        >
                            {{ __('profile.password_required_for_email_change') }}
                        </p>

                        @error('current_password')
                            <p
                                class="mt-2 rounded-xl bg-red-50 p-3 text-sm font-semibold text-red-600"
                                role="alert"
                            >
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div
                        class="grid grid-cols-1 md:grid-cols-2 gap-6"
                    >
                        <div>
                            <label
                                for="city"
                                class="block text-sm font-semibold text-gray-600 mb-2 px-1"
                            >
                                {{ __('profile.city') }}
                            </label>

                            <input
                                type="text"
                                id="city"
                                name="city"
                                value="{{ old('city', $user->city) }}"
                                class="w-full bg-pink-50/50 border-0 rounded-2xl px-4 py-3 focus:ring-2 focus:ring-pink-400 focus:bg-white transition-all outline-none"
                            >
                        </div>

                        <div>
                            <label
                                for="marital_status"
                                class="block text-sm font-semibold text-gray-600 mb-2 px-1"
                            >
                                {{ __('profile.marital_status') }}
                            </label>

                            <select
                                id="marital_status"
                                name="marital_status"
                                class="w-full bg-pink-50/50 border-0 rounded-2xl px-4 py-3 focus:ring-2 focus:ring-pink-400 focus:bg-white transition-all outline-none appearance-none"
                            >
                                <option value="">
                                    {{ __('profile.select') }}
                                </option>

                                <option
                                    value="single"
                                    {{ old('marital_status', $user->marital_status) == 'single' ? 'selected' : '' }}
                                >
                                    {{ __('profile.single') }}
                                </option>

                                <option
                                    value="married"
                                    {{ old('marital_status', $user->marital_status) == 'married' ? 'selected' : '' }}
                                >
                                    {{ __('profile.married') }}
                                </option>

                                <option
                                    value="divorced"
                                    {{ old('marital_status', $user->marital_status) == 'divorced' ? 'selected' : '' }}
                                >
                                    {{ __('profile.divorced') }}
                                </option>

                                <option
                                    value="widowed"
                                    {{ old('marital_status', $user->marital_status) == 'widowed' ? 'selected' : '' }}
                                >
                                    {{ __('profile.widowed') }}
                                </option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label
                            for="bio"
                            class="block text-sm font-semibold text-gray-600 mb-2 px-1"
                        >
                            {{ __('profile.bio') }}
                        </label>

                        <textarea
                            id="bio"
                            name="bio"
                            rows="4"
                            placeholder="درباره خودت بنویس..."
                            class="w-full bg-pink-50/50 border-0 rounded-3xl px-4 py-3 focus:ring-2 focus:ring-pink-400 focus:bg-white transition-all outline-none resize-none"
                        >{{ old('bio', $user->bio) }}</textarea>
                    </div>

                    <div class="relative group">
                        <label
                            class="block text-sm font-semibold text-gray-600 mb-2 px-1"
                        >
                            {{ __('profile.profile_picture') }}
                        </label>

                        <div
                            class="flex items-center justify-center w-full"
                        >
                            <label
                                for="profile_picture"
                                class="flex flex-col items-center justify-center w-full h-32 border-2 border-pink-200 border-dashed rounded-3xl cursor-pointer bg-pink-50/30 group-hover:bg-pink-50 group-hover:border-pink-300 transition-all"
                            >
                                <div
                                    class="flex flex-col items-center justify-center pt-5 pb-6"
                                >
                                    <svg
                                        class="w-8 h-8 mb-3 text-pink-400"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M12 6v6m0 0v6m0-6h6m-6 0H6"
                                        ></path>
                                    </svg>

                                    <p
                                        class="text-sm text-gray-500 italic"
                                    >
                                        یک تصویر خیره‌کننده انتخاب کنید
                                    </p>
                                </div>

                                <input
                                    type="file"
                                    id="profile_picture"
                                    name="profile_picture"
                                    accept="image/*"
                                    class="hidden"
                                >
                            </label>
                        </div>

                        <div class="mt-4 flex justify-center">
                            <img
                                id="preview"
                                src="{{ $user->profilePhotoUrl() }}"
                                alt="پیش‌نمایش تصویر پروفایل"
                                class="w-20 h-20 rounded-2xl object-cover border-2 border-pink-100 shadow-sm"
                            >
                        </div>
                    </div>

                    <div class="flex justify-center pt-4">
                        <button
                            type="submit"
                            class="w-full md:w-2/3 bg-gradient-to-r from-rose-500 to-pink-500 hover:from-rose-600 hover:to-pink-600 text-white font-bold py-4 rounded-2xl shadow-lg shadow-pink-200 transition-all transform hover:-translate-y-1 active:scale-95"
                        >
                            {{ __('profile.save') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script nonce="{{ \Illuminate\Support\Facades\Vite::cspNonce() }}">
        document
            .getElementById('profile_picture')
            ?.addEventListener('change', previewImage);

        function previewImage(event) {
            const file = event.target.files[0];

            if (!file) {
                return;
            }

            const reader = new FileReader();

            reader.onload = () => {
                document.getElementById('preview').src =
                    reader.result;

                document.getElementById('header-avatar').src =
                    reader.result;
            };

            reader.readAsDataURL(file);
        }
    </script>
@endsection
