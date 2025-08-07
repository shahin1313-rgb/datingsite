@extends('layouts.app')

@section('content')
    <div class="min-h-screen flex justify-center items-center px-4 bg-[#1C1C1C]">
        <div class="w-full max-w-4xl">
            <div class="bg-[#2F2F2F] shadow-lg rounded-lg overflow-hidden text-white">

                {{-- Header --}}
                <div class="bg-[#9B59B6] px-6 py-4 border-b border-[#F1C40F] text-xl font-bold tracking-wide text-white">
                    {{ __('داشبورد همسریابی') }}
                </div>

                {{-- Search Form --}}
                <h1 class="text-3xl text-center mt-6 font-extrabold text-[#F1C40F]">جستجوی پروفایل‌ها</h1>

                <form action="{{ route('search') }}" method="GET" class="p-6 space-y-4 text-right">

                    {{-- City --}}
                    <div>
                        <label for="city" class="block text-[#F1C40F] font-semibold mb-1">شهر:</label>
                        <input type="text" id="city" name="city" value="{{ request('city') }}"
                            placeholder="مثلاً تهران"
                            class="w-full bg-[#1C1C1C] text-white border border-[#9B59B6] rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#E74C3C]">
                    </div>

                    {{-- Age Range --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="min_age" class="block text-[#F1C40F] font-semibold mb-1">حداقل سن:</label>
                            <input type="number" id="min_age" name="min_age" value="{{ request('min_age') }}"
                                class="w-full bg-[#1C1C1C] text-white border border-[#9B59B6] rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#E74C3C]">
                        </div>
                        <div>
                            <label for="max_age" class="block text-[#F1C40F] font-semibold mb-1">حداکثر سن:</label>
                            <input type="number" id="max_age" name="max_age" value="{{ request('max_age') }}"
                                class="w-full bg-[#1C1C1C] text-white border border-[#9B59B6] rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#E74C3C]">
                        </div>
                    </div>

                    {{-- Marital Status --}}
                    <div>
                        <label for="marital_status" class="block text-[#F1C40F] font-semibold mb-1">وضعیت تأهل:</label>
                        <select name="marital_status" id="marital_status"
                            class="w-full bg-[#1C1C1C] text-white border border-[#9B59B6] rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#E74C3C]">
                            <option value="">-- انتخاب کنید --</option>
                            <option value="single" {{ request('marital_status') == 'single' ? 'selected' : '' }}>مجرد
                            </option>
                            <option value="married" {{ request('marital_status') == 'married' ? 'selected' : '' }}>متأهل
                            </option>
                            <option value="divorced" {{ request('marital_status') == 'divorced' ? 'selected' : '' }}>جدا شده
                            </option>
                        </select>
                    </div>

                    {{-- Interests --}}
                    <div>
                        <label for="interests" class="block text-[#F1C40F] font-semibold mb-1">علایق:</label>
                        <input type="text" name="interests" id="interests" value="{{ request('interests') }}"
                            placeholder="مثلاً موسیقی، ورزش"
                            class="w-full bg-[#1C1C1C] text-white border border-[#9B59B6] rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#E74C3C]">
                    </div>

                    {{-- Has Photo --}}
                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="has_photo" id="has_photo" value="1"
                            {{ request('has_photo') ? 'checked' : '' }}
                            class="w-4 h-4 text-[#E74C3C] bg-[#1C1C1C] border-[#9B59B6] rounded">
                        <label for="has_photo" class="text-white">فقط کاربران دارای عکس</label>
                    </div>

                    {{-- Is Active --}}
                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="is_active" id="is_active" value="1"
                            {{ request('is_active') ? 'checked' : '' }}
                            class="w-4 h-4 text-[#E74C3C] bg-[#1C1C1C] border-[#9B59B6] rounded">
                        <label for="is_active" class="text-white">فقط کاربران فعال</label>
                    </div>

                    {{-- Submit --}}
                    <div class="text-center mt-6">
                        <button type="submit"
                            class="bg-[#E74C3C] text-white px-6 py-2 rounded-full hover:bg-red-700 shadow-lg transition duration-200 font-bold">
                            🔍 جستجو
                        </button>
                    </div>
                </form>

                {{-- Search Results --}}
                <div class="p-6">
                    @if ($profiles->isEmpty())
                        <p class="text-center text-gray-400">هیچ پروفایلی یافت نشد.</p>
                    @else
                        <ul class="space-y-6">
                            @foreach ($profiles as $profile)
                                <li
                                    class="border border-[#9B59B6] rounded-lg p-4 flex flex-col items-center bg-[#1C1C1C] text-white">
                                    @if ($profile->profile_picture)
                                        <img src="{{ asset('storage/' . $profile->profile_picture) }}" alt="avatar"
                                            class="w-24 h-24 rounded-full shadow-md mb-3 object-cover ring-2 ring-[#F1C40F]" />
                                    @else
                                        <p class="text-sm text-gray-400">عکس پروفایل موجود نیست.</p>
                                    @endif

                                    <div class="text-center">
                                        <h5 class="text-lg font-bold text-[#F1C40F]">{{ $profile->name }}</h5>
                                        <p class="text-sm text-gray-300">
                                            {{ $profile->city }} | سن: {{ $profile->age }} | تولد:
                                            {{ $profile->birth_year }}
                                        </p>
                                        <p class="text-sm mt-1 text-gray-400">{{ $profile->email }}</p>
                                        <p class="text-sm mt-1 text-gray-400">{{ $profile->bio }}</p>

                                        <div class="flex justify-center gap-3 mt-4">
                                            <a href="{{ route('messages.show', $profile) }}"
                                                class="bg-[#E74C3C] text-white px-4 py-2 rounded hover:bg-red-700 text-sm shadow">
                                                💌 پیام بده
                                            </a>
                                            <a href="{{ route('profile.show', $profile->id) }}"
                                                class="bg-[#9B59B6] text-white px-4 py-2 rounded hover:bg-purple-800 text-sm shadow">
                                                👀 مشاهده پروفایل
                                            </a>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>

                        {{-- Pagination --}}
                        <div class="mt-6 flex justify-center">
                            {{ $profiles->appends(request()->query())->links() }}
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </div>
@endsection
