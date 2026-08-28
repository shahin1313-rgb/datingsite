@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                   
<div class="min-h-screen bg-[#121212] py-10 px-4">
    <div class="max-w-6xl mx-auto">
        
        {{-- Header Section --}}
        <div class="text-center mb-10">
            <h1 class="text-4xl font-extrabold text-[#F1C40F] mb-2">نیمه گمشده‌ات را پیدا کن</h1>
            <p class="text-gray-400">جستجو در میان هزاران پروفایل فعال</p>
        </div>

        {{-- Glassmorphism Search Form --}}
        <div class="bg-[#1E1E1E] border border-gray-800 p-8 rounded-3xl shadow-2xl mb-12">
            <form action="{{ route('search') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-6 text-right" dir="rtl">
                
                {{-- City --}}
                <div class="flex flex-col">
                    <label class="text-gray-300 mb-2 mr-1">📍 شهر</label>
                    <input type="text" name="city" value="{{ request('city') }}" placeholder="کدام شهر؟"
                        class="bg-[#2A2A2A] border-none text-white rounded-xl px-4 py-3 focus:ring-2 focus:ring-[#9B59B6] transition">
                </div>

                {{-- Age Range --}}
                <div class="flex flex-col">
                    <label class="text-gray-300 mb-2 mr-1">🎂 محدوده سنی</label>
                    <div class="flex gap-2">
                        <input type="number" name="min_age" value="{{ request('min_age') }}" placeholder="از"
                            class="w-1/2 bg-[#2A2A2A] border-none text-white rounded-xl px-4 py-3 focus:ring-2 focus:ring-[#9B59B6]">
                        <input type="number" name="max_age" value="{{ request('max_age') }}" placeholder="تا"
                            class="w-1/2 bg-[#2A2A2A] border-none text-white rounded-xl px-4 py-3 focus:ring-2 focus:ring-[#9B59B6]">
                    </div>
                </div>

                {{-- Marital Status --}}
                <div class="flex flex-col">
                    <label class="text-gray-300 mb-2 mr-1">💍 وضعیت تأهل</label>
                    <select name="marital_status" class="bg-[#2A2A2A] border-none text-white rounded-xl px-4 py-3 focus:ring-2 focus:ring-[#9B59B6]">
                        <option value="">همه موارد</option>
                        <option value="single" {{ request('marital_status') == 'single' ? 'selected' : '' }}>مجرد</option>
                        <option value="divorced" {{ request('marital_status') == 'divorced' ? 'selected' : '' }}>جدا شده</option>
                    </select>
                </div>

                {{-- Checkboxes --}}
                <div class="md:col-span-2 flex flex-wrap gap-6 items-center">
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="has_photo" value="1" {{ request('has_photo') ? 'checked' : '' }} class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#E74C3C]"></div>
                        <span class="mr-3 text-sm font-medium text-gray-300">فقط عکس‌دارها</span>
                    </label>

                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" {{ request('is_active') ? 'checked' : '' }} class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#2ECC71]"></div>
                        <span class="mr-3 text-sm font-medium text-gray-300">آنلاین‌ها</span>
                    </label>
                </div>

                {{-- Submit Button --}}
                <div class="flex items-end">
                    <button type="submit" class="w-full bg-gradient-to-r from-[#9B59B6] to-[#E74C3C] text-white font-bold py-3 rounded-xl hover:opacity-90 transition transform hover:-translate-y-1 shadow-lg">
                        جستجوی پیشرفته
                    </button>
                </div>
            </form>
        </div>

        {{-- Results Grid --}}
        <div class="p-2">
            @if ($profiles->isEmpty())
                <div class="text-center py-20 bg-[#1E1E1E] rounded-3xl">
                    <p class="text-gray-500 text-xl">متأسفانه کسی با این مشخصات پیدا نشد 🔎</p>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach ($profiles as $profile)
                        <div class="bg-[#1E1E1E] rounded-3xl overflow-hidden border border-gray-800 hover:border-[#9B59B6] transition-all group">
                            {{-- Profile Image --}}
                            <div class="relative h-64 overflow-hidden">
                                <img src="{{ $profile->profilePhotoUrl() }}"
                                     class="w-full h-full object-cover group-hover:scale-110 transition duration-500" alt="avatar">
                                <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-[#1E1E1E] to-transparent h-24"></div>
                                @if($profile->isOnline())
                                    <span class="absolute top-4 right-4 bg-green-500 w-3 h-3 rounded-full border-2 border-white animate-pulse"></span>
                                @endif
                            </div>

                            {{-- Profile Info --}}
                            <div class="p-6 text-center">
                                <h3 class="text-xl font-bold text-white mb-1">{{ $profile->name }}</h3>
                                <p class="text-[#F1C40F] text-sm mb-4">{{ $profile->city }} • {{ $profile->age }} ساله</p>
                                <p class="text-gray-400 text-sm line-clamp-2 mb-6 h-10">{{ $profile->bio ?? 'بیوگرافی ثبت نشده است.' }}</p>

                                {{-- Actions --}}
                                <div class="flex gap-2 justify-center">
                                    <a href="{{ route('profile.show', $profile->id) }}" 
                                       class="flex-1 bg-[#2A2A2A] text-white py-2 rounded-lg hover:bg-gray-700 transition text-sm">
                                        پروفایل
                                    </a>
                                    <a href="{{ route('messages.show', $profile) }}" 
                                       class="flex-1 bg-[#E74C3C] text-white py-2 rounded-lg hover:bg-[#c0392b] transition text-sm flex items-center justify-center gap-1">
                                        <span>💌</span> پیام
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Pagination --}}
                <div class="mt-12">
                    {{ $profiles->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    /* سفارشی‌سازی اسکرول‌بار و صفحه‌بندی برای تم تیره */
    .pagination { @apply flex justify-center gap-2; }
    .page-item.active .page-link { @apply bg-[#9B59B6] border-[#9B59B6]; }
    .page-link { @apply bg-[#1E1E1E] border-gray-800 text-gray-400 rounded-lg; }
</style>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
