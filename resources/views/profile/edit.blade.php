@extends('layouts.app')

@section('content')
    <div class="container mx-auto max-w-2xl py-10 px-4" dir="rtl">
        <div class="bg-white shadow-lg rounded-3xl overflow-hidden">
            <!-- Header -->
            <div class="relative bg-gradient-to-tr from-indigo-500 via-blue-500 to-sky-400 h-40">
                <div class="absolute -bottom-12 right-6 flex items-center">
                    <div class="relative">
                        <img src="{{ asset('storage/' . $user->profile_picture) }}?v={{ time() }} "
                            class="w-24 h-24 rounded-full border-4 border-white shadow-md object-cover" alt="Profile Picture">
                        <button
                            class="absolute bottom-0 left-0 bg-white text-indigo-600 rounded-full p-1.5 shadow hover:bg-indigo-100 transition"
                            title="تغییر تصویر پروفایل">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 9a2 2 0 012-2h6.93a2 2 0 001.664.89l.812-.162a2 2 0 011.415.586l4.242 4.242a2 2 0 010 2.828l-4.242 4.242a2 2 0 01-2.828 0l-4.242-4.242a2 2 0 01-.586-1.415l.162-.812A2 2 0 007 11.07V9z" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Info -->
            <div class="pt-16 text-center px-6 pb-4">
                <h2 class="text-xl font-semibold text-gray-800">{{ $user->name }}</h2>
                <p class="text-sm text-gray-500 mt-1">طراح محصول حرفه‌ای</p>
            </div>

            <!-- Form -->
            <div class="px-6 pb-8">
                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                    @csrf

                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-600 mb-1">نام</label>
                        <input type="text" id="name" name="name" value="{{ $user->name }}"
                            class="w-full border border-gray-300 rounded-xl px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none transition"
                            required>
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-600 mb-1">ایمیل</label>
                        <input type="email" id="email" name="email" value="{{ $user->email }}"
                            class="w-full border border-gray-300 rounded-xl px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none transition"
                            required>
                    </div>

                    <div>
                        <label for="city" class="block text-sm font-medium text-gray-600 mb-1">شهر</label>
                        <input type="text" id="city" name="city" value="{{ $user->city }}"
                            class="w-full border border-gray-300 rounded-xl px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none transition">
                    </div>
                    <div class="mt-4">
                        <label for="marital_status" class="block text-sm font-medium text-gray-700">وضعیت تأهل</label>
                        <select id="marital_status" name="marital_status"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            <option value="">انتخاب کنید</option>
                            <option value="single"
                                {{ old('marital_status', $user->marital_status) == 'single' ? 'selected' : '' }}>مجرد
                            </option>
                            <option value="married"
                                {{ old('marital_status', $user->marital_status) == 'married' ? 'selected' : '' }}>متأهل
                            </option>
                            <option value="divorced"
                                {{ old('marital_status', $user->marital_status) == 'divorced' ? 'selected' : '' }}>
                                طلاق‌گرفته</option>
                            <option value="widowed"
                                {{ old('marital_status', $user->marital_status) == 'widowed' ? 'selected' : '' }}>همسر فوت
                                شده</option>
                        </select>
                    </div>


                    <div>
                        <label for="bio" class="block text-sm font-medium text-gray-600 mb-1">بیوگرافی</label>
                        <textarea id="bio" name="bio" rows="4"
                            class="w-full border border-gray-300 rounded-xl px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none transition">{{ $user->bio }}</textarea>
                    </div>
                    <div class="relative">
                        <label class="block text-sm font-medium text-gray-700 mb-1">تصویر پروفایل</label>
                        <!-- پیش‌نمایش -->
                        <img id="preview" src="{{ asset('storage/' . $user->profile_picture) }}" alt="Profile Picture"
                            class="w-24 h-24 rounded-full border-2 border-gray-300 mt-2 object-cover">
                        <input type="file" id="profile_picture" name="profile_picture" accept="image/*"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" onchange="previewImage(event)">

                    </div>



                    <div class="flex justify-end">
                        <button type="submit"
                            class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium px-6 py-2 rounded-xl transition duration-200">
                            ذخیره تغییرات
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Vazirmatn:wght@400;600;700&display=swap');

        body {
            font-family: 'Vazirmatn', sans-serif;
            background-color: #f8fafc;
        }

        input,
        textarea {
            font-family: inherit;
        }
    </style>
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
