@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-10 px-4">
    <div class="max-w-6xl mx-auto">
        
        {{-- Header Section --}}
        <div class="text-center mb-10">
            <h1 class="text-4xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-pink-600 to-rose-400 mb-2">
                {{ __('ui.find_match') }}
            </h1>
            <p class="text-gray-500 font-medium">{{ __('ui.search_subtitle') }}</p>
        </div>

        @if ($errors->any())
            <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 p-4 text-red-700" role="alert">
                <p class="font-bold mb-2">{{ __('ui.search_errors') }}</p>
                <ul class="list-disc list-inside text-sm space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Search Form - Soft White Card --}}
        <div class="bg-white border border-pink-100 p-8 rounded-[2rem] shadow-xl shadow-pink-100/50 mb-12">
            <form action="{{ route('search') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-6 {{ app()->isLocale('fa') ? 'text-right' : 'text-left' }}" dir="{{ app()->isLocale('fa') ? 'rtl' : 'ltr' }}">
                
                {{-- City --}}
                <div class="flex flex-col">
                    <label for="city" class="text-gray-600 mb-2 mr-1 font-semibold">📍 {{ __('ui.city') }}</label>
                    <input id="city" type="text" name="city" value="{{ request('city') }}" placeholder="{{ __('ui.city_placeholder') }}"
                        aria-invalid="{{ $errors->has('city') ? 'true' : 'false' }}"
                        class="bg-gray-50 border-gray-200 text-gray-700 rounded-2xl px-4 py-3 focus:ring-2 focus:ring-pink-400 focus:bg-white transition outline-none">
                </div>

                {{-- Age Range --}}
                <div class="flex flex-col">
                    <label class="text-gray-600 mb-2 mr-1 font-semibold">🎂 {{ __('ui.age_range') }}</label>
                    <div class="flex gap-2">
                        <input type="number" name="min_age" min="18" max="100" value="{{ request('min_age') }}" placeholder="{{ __('ui.from') }}"
                            aria-label="{{ __('ui.minimum_age') }}" aria-invalid="{{ $errors->has('min_age') ? 'true' : 'false' }}"
                            class="w-1/2 bg-gray-50 border-gray-200 rounded-2xl px-4 py-3 focus:ring-2 focus:ring-pink-400 outline-none">
                        <input type="number" name="max_age" min="18" max="100" value="{{ request('max_age') }}" placeholder="{{ __('ui.to') }}"
                            aria-label="{{ __('ui.maximum_age') }}" aria-invalid="{{ $errors->has('max_age') ? 'true' : 'false' }}"
                            class="w-1/2 bg-gray-50 border-gray-200 rounded-2xl px-4 py-3 focus:ring-2 focus:ring-pink-400 outline-none">
                    </div>
                </div>

                {{-- Marital Status --}}
               <div class="flex flex-col w-full max-w-xs font-sans">
    <label for="marital_status" class="flex items-center text-gray-700 mb-2 ms-1 text-sm font-bold">
        <span class="ml-2 text-base">💍</span>
        {{ __('ui.marital_status') }}
    </label>

    <div class="relative group">
        <select id="marital_status" name="marital_status" 
            class="w-full appearance-none bg-white border border-gray-200 text-gray-700 py-3 px-4 pe-10 rounded-2xl 
                   transition-all duration-300 ease-in-out
                   focus:bg-white focus:border-pink-400 focus:ring-4 focus:ring-pink-100 focus:outline-none
                   cursor-pointer shadow-sm hover:border-gray-300">
            
            <option value="" class="py-2">{{ __('ui.all') }}</option>
            <option value="single" @selected(request('marital_status') === 'single')>{{ __('ui.single') }}</option>
            <option value="married" @selected(request('marital_status') === 'married')>{{ __('ui.married') }}</option>
            <option value="divorced" @selected(request('marital_status') === 'divorced')>{{ __('ui.divorced') }}</option>
            <option value="widowed" @selected(request('marital_status') === 'widowed')>{{ __('ui.widowed') }}</option>
        </select>

        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center px-4 text-gray-400 group-focus-within:text-pink-500 transition-colors">
            <svg class="h-4 w-4 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                <path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" />
            </svg>
        </div>
    </div>
</div>

                {{-- Interests --}}
                <div class="flex flex-col w-full max-w-xs">
                    <label for="interested_in" class="text-gray-700 mb-2 ms-1 text-sm font-bold">
                        ✨ {{ __('ui.interests') }}
                    </label>
                    <select id="interested_in" name="interested_in"
                        class="w-full bg-white border border-gray-200 text-gray-700 py-3 px-4 rounded-2xl focus:border-pink-400 focus:ring-4 focus:ring-pink-100 focus:outline-none">
                        <option value="">{{ __('ui.any_interest') }}</option>
                        <option value="sport" @selected(request('interested_in') === 'sport')>{{ __('ui.sport') }}</option>
                        <option value="travel" @selected(request('interested_in') === 'travel')>{{ __('ui.travel') }}</option>
                        <option value="books" @selected(request('interested_in') === 'books')>{{ __('ui.books') }}</option>
                        <option value="party" @selected(request('interested_in') === 'party')>{{ __('ui.party') }}</option>
                    </select>
                </div>

                {{-- Checkboxes --}}
                <div class="md:col-span-2 flex flex-wrap gap-6 items-center">
                   <div class="flex flex-wrap gap-6 items-center font-sans">
    
    <label class="group relative flex items-center cursor-pointer select-none">
        <input type="checkbox" name="has_photo" value="1" {{ request('has_photo') ? 'checked' : '' }} class="sr-only peer">
        
        <div class="w-12 h-6 bg-gray-300 rounded-full transition-colors duration-300 ease-in-out 
                    peer-checked:bg-pink-500 peer-focus:ring-4 peer-focus:ring-pink-200">
        </div>
        
        <div class="absolute right-1 top-1 w-4 h-4 bg-white rounded-full transition-transform duration-300 ease-in-out 
                    peer-checked:-translate-x-6 shadow-sm">
        </div>
        
        <span class="ms-3 text-sm font-semibold text-gray-700 group-hover:text-gray-900 transition-colors">
            {{ __('ui.photo_only') }}
        </span>
    </label>

    <label class="group relative flex items-center cursor-pointer select-none">
        <input type="checkbox" name="is_active" value="1" {{ request('is_active') ? 'checked' : '' }} class="sr-only peer">
        
        <div class="w-12 h-6 bg-gray-300 rounded-full transition-colors duration-300 ease-in-out 
                    peer-checked:bg-emerald-500 peer-focus:ring-4 peer-focus:ring-emerald-200">
        </div>
        
        <div class="absolute right-1 top-1 w-4 h-4 bg-white rounded-full transition-transform duration-300 ease-in-out 
                    peer-checked:-translate-x-6 shadow-sm">
        </div>
        
        <span class="ms-3 text-sm font-semibold text-gray-700 group-hover:text-gray-900 transition-colors">
            {{ __('ui.online_only') }}
        </span>
    </label>

</div>
                </div>

                {{-- Submit Button --}}
                <div class="flex items-end">
                    <button type="submit" class="w-full bg-gradient-to-r from-pink-600 to-rose-500 text-white font-bold py-3 rounded-2xl hover:shadow-lg hover:shadow-pink-300 transition transform hover:-translate-y-1 active:scale-95">
                        <i class="fas fa-search ml-2"></i> {{ __('ui.advanced_search') }}
                    </button>
                </div>
            </form>
        </div>

        {{-- Results Grid --}}
        <div class="p-2">
            @if ($profiles->isEmpty())
                <div class="text-center py-20 bg-white rounded-[2rem] border border-dashed border-pink-200">
                    <div class="text-6xl mb-4">🔎</div>
                    <p class="text-gray-400 text-xl font-medium">{{ __('ui.no_results') }}</p>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach ($profiles as $profile)
                        <div class="bg-white rounded-[2rem] overflow-hidden shadow-sm hover:shadow-2xl transition-all duration-300 border border-gray-100 group">
                            {{-- Profile Image --}}
                            <div class="relative h-72 overflow-hidden">
                                <img src="{{ $profile->profilePhotoUrl() }}"
                                     class="w-full h-full object-cover group-hover:scale-105 transition duration-700" alt="avatar">
                                
                                {{-- Status Badge --}}
                                @if($profile->isOnline())
                                    <div class="absolute top-4 left-4 bg-white/80 backdrop-blur-md px-3 py-1 rounded-full flex items-center gap-1">
                                        <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                                        <span class="text-[10px] font-bold text-gray-700">{{ __('ui.online') }}</span>
                                    </div>
                                @endif
                                
                                <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-white via-white/20 to-transparent h-20"></div>
                            </div>

                            {{-- Profile Info --}}
                            <div class="p-6 text-center">
                                <h3 class="text-xl font-extrabold text-gray-800 mb-1">{{ $profile->name }}</h3>
                                <div class="flex justify-center items-center gap-2 text-pink-500 text-sm mb-3">
                                    <span class="bg-pink-50 px-3 py-1 rounded-full font-bold">{{ $profile->city }}</span>
                                    <span class="bg-pink-50 px-3 py-1 rounded-full font-bold">{{ __('ui.age_years', ['age' => $profile->age]) }}</span>
                                </div>
                                <p class="text-gray-500 text-sm line-clamp-2 mb-6 h-10 leading-relaxed">{{ $profile->bio ?? __('ui.no_bio') }}</p>

                                {{-- Actions --}}
                                <div class="flex gap-3 justify-center">
                                    <a href="{{ route('profile.show', $profile->id) }}" 
                                       class="flex-1 bg-gray-100 text-gray-700 font-bold py-2.5 rounded-xl hover:bg-gray-200 transition text-sm">
                                         {{ __('ui.view_profile') }}
                                    </a>
                                    <a href="{{ route('messages.show', $profile) }}" 
                                       class="flex-1 bg-gradient-to-r from-pink-500 to-rose-400 text-white font-bold py-2.5 rounded-xl hover:opacity-90 transition text-sm flex items-center justify-center gap-2 shadow-md shadow-pink-200">
                                        <i class="fas fa-heart"></i> {{ __('ui.send_message') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Pagination --}}
                <div class="mt-12 flex justify-center custom-pagination">
                    {{ $profiles->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<style>
   /* نمایش اجباری کانتینر پجینیشن */
    .custom-pagination nav {
        display: flex !important;
        flex-direction: column !important; /* دکمه‌ها بالا، متن پایین */
        visibility: visible !important;
        opacity: 1 !important;
        justify-content: center;
        align-items: center;
        gap: 16px;
    }

    /* ۱. حذف قطعی دکمه‌های موبایلی (کدی که فرستادید) */
    .custom-pagination nav > div.flex.justify-between.flex-1.sm\:hidden {
        display: none !important;
    }

    .custom-pagination ul {
        display: flex !important;
        list-style: none !important;
        padding: 0 !important;
    }

    /* ۲. نمایش آمار (Showing...) در ردیف دوم */
    .custom-pagination nav > div:not(.sm\:hidden):first-child {
        display: block !important;
        order: 2; /* انتقال به پایین */
    }
    
    .custom-pagination nav > div p {
        color: #9ca3af !important;
        font-size: 0.875rem !important;
        text-align: center;
        margin-top: 8px;
    }

    /* ۳. نمایش شماره صفحات در ردیف اول */
    .custom-pagination nav > div:last-child {
        display: flex !important;
        flex-direction: row !important;
        order: 1; /* انتقال به بالا */
    }

    /* استایل دکمه‌ها (بدون تغییر طبق درخواست شما) */
    .custom-pagination a, 
    .custom-pagination span {
        display: flex !important;
        padding: 10px 16px !important;
        margin: 0 4px !important;
        border-radius: 12px !important;
        background-color: white !important;
        color: #db2777 !important;
        font-weight: bold !important;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1) !important;
        text-decoration: none !important;
        transition: all 0.2s;
    }

    /* صفحه فعال */
    .custom-pagination .active span,
    .custom-pagination li[aria-current="page"] span {
        background: linear-gradient(to right, #ec4899, #f43f5e) !important;
        color: white !important;
        box-shadow: 0 4px 12px rgba(236, 72, 153, 0.3) !important;
    }

    .custom-pagination a:hover {
        background-color: #fdf2f8 !important;
        transform: translateY(-2px);
    }

    .custom-pagination svg {
        width: 20px !important;
        height: 20px !important;
    }
    /* حذف متن Showing... (تعداد نتایج) */
    .custom-pagination nav div p {
        display: none !important;
    }
    /* هدف قرار دادن شماره صفحه فعال */
.custom-pagination [aria-current="page"] span {
    background-color: #fdf2f8 !important; /* صورتی بسیار ملایم (Pink 50) */
    color: #db2777 !important;           /* رنگ متن صورتی تند (Pink 600) */
    border: 1px solid #f9a8d4 !important; /* حاشیه صورتی روشن (Pink 300) */
    font-weight: 800 !important;          /* ضخیم‌تر کردن شماره صفحه */
    box-shadow: 0 2px 8px rgba(219, 39, 119, 0.15) !important; /* سایه ملایم صورتی */
    z-index: 10;
}
</style>
@endsection
