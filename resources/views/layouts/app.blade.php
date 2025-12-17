<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'fa' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'DatingApp') }}</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/alpinejs" defer></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    @yield('head')

    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Tahoma', sans-serif; -webkit-tap-highlight-color: transparent; }
        .pb-safe { padding-bottom: calc(4rem + env(safe-area-inset-bottom)); }
        
        /* استایل کمکی برای انیمیشن محو شدن لودینگ */
        .loader-hidden {
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.5s ease-out;
        }
    </style>
</head>

<body class="bg-gray-50 text-gray-900">

    {{-- لودینگ مدرن و هوشمند --}}
    <div id="globalLoading" class="fixed inset-0 bg-white flex flex-col items-center justify-center z-[9999]">
        <div class="relative">
            <div class="w-16 h-16 border-4 border-pink-100 border-t-pink-600 rounded-full animate-spin"></div>
            <div class="absolute inset-0 flex items-center justify-center">
                <i class="fas fa-heart text-pink-500 animate-pulse"></i>
            </div>
        </div>
        <span class="mt-4 text-sm font-medium text-gray-500">در حال بارگذاری...</span>
    </div>

    <div id="app" class="flex flex-col min-h-screen">
        {{-- نوبار --}}
        <nav class="bg-white/80 backdrop-blur-md border-b sticky top-0 z-50 h-14 flex items-center">
            <div class="container mx-auto px-4 flex justify-between items-center">
                <a href="{{ url('/') }}" class="flex items-center gap-2">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-8 w-8">
                    <span class="font-bold text-lg tracking-tight text-pink-600">DatingApp</span>
                </a>

                <div class="flex items-center gap-4">
                   <div class="relative" x-data="{ langMenu: false }">
                        <button @click="langMenu = !langMenu" class="text-gray-500 text-sm focus:outline-none p-2">
                            <i class="fa-solid fa-globe"></i>
                        </button>
                        
                        <div x-show="langMenu" 
                             @click.away="langMenu = false" 
                             x-cloak 
                             class="absolute left-0 mt-2 w-32 bg-white shadow-2xl border border-gray-100 rounded-xl overflow-hidden z-[100]">
                            <a href="{{ url('lang/fa') }}" class="flex items-center gap-2 px-4 py-3 text-sm text-gray-700 hover:bg-pink-50 transition-colors">🇮🇷 فارسی</a>
                            <a href="{{ url('lang/en') }}" class="flex items-center gap-2 px-4 py-3 text-sm text-gray-700 hover:bg-pink-50 transition-colors">🇬🇧 English</a>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        {{-- محتوای اصلی --}}
        <main class="flex-grow p-4 pb-safe">
            <div class="max-w-md mx-auto"> 
                @yield('content')
            </div>
        </main>

        {{-- منوی پایین موبایل --}}
        <nav class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 px-2 py-1 z-50 sm:hidden">
            <div class="flex justify-around items-center h-14">
                <a href="{{ url('/') }}" class="flex flex-col items-center justify-center w-full {{ request()->is('/') ? 'text-pink-600' : 'text-gray-400' }}">
                    <i class="fas fa-heart text-xl"></i>
                    <span class="text-[10px] mt-1">اکتشاف</span>
                </a>

                <a href="{{ route('search') }}" class="flex flex-col items-center justify-center w-full {{ request()->routeIs('search') ? 'text-pink-600' : 'text-gray-400' }}">
                    <i class="fas fa-search text-xl"></i>
                    <span class="text-[10px] mt-1">جستجو</span>
                </a>

                <a href="{{ route('messages.index') }}" class="flex flex-col items-center justify-center w-full relative {{ request()->routeIs('messages.*') ? 'text-pink-600' : 'text-gray-400' }}">
                    <i class="fas fa-comment-dots text-xl"></i>
                    <span class="text-[10px] mt-1">پیام‌ها</span>
                    @if(isset($globalUnreadCount) && $globalUnreadCount > 0)
                        <span class="absolute top-1 right-4 bg-red-500 text-white text-[10px] font-bold px-1.5 rounded-full border-2 border-white">
                            {{ $globalUnreadCount }}
                        </span>
                    @endif
                </a>

                <a href="{{ route('dashboard') }}" class="flex flex-col items-center justify-center w-full {{ request()->routeIs('dashboard') ? 'text-pink-600' : 'text-gray-400' }}">
                    <i class="fas fa-user-circle text-xl"></i>
                    <span class="text-[10px] mt-1">پروفایل</span>
                </a>
            </div>
        </nav>
    </div>

    {{-- اسکریپت کنترل لودینگ --}}
    <script>
        function hideGlobalLoader() {
            const loader = document.getElementById('globalLoading');
            if (loader) {
                loader.classList.add('loader-hidden');
                setTimeout(() => {
                    loader.style.display = 'none';
                }, 500);
            }
        }

        // مخفی کردن لودینگ بعد از لود کامل صفحه
        window.addEventListener('load', hideGlobalLoader);

        // لایه محافظ: اگر لودینگ تا ۳ ثانیه مخفی نشد، اجباراً مخفی شود
        setTimeout(hideGlobalLoader, 3000);
    </script>

    @stack('scripts')
</body>
</html>