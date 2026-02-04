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
        .loader-hidden { opacity: 0; pointer-events: none; transition: opacity 0.5s ease-out; }
    </style>
</head>

<body class="bg-gray-50 text-gray-900">

    {{-- لودینگ --}}
    <div id="globalLoading" class="fixed inset-0 bg-white flex flex-col items-center justify-center z-[9999]">
        <div class="relative">
            <div class="w-16 h-16 border-4 border-pink-100 border-t-pink-600 rounded-full animate-spin"></div>
            <div class="absolute inset-0 flex items-center justify-center">
                <i class="fas fa-heart text-pink-500 animate-pulse"></i>
            </div>
        </div>
        <span class="mt-4 text-sm font-medium text-gray-500">در حال بارگذاری...</span>
    </div>

    {{-- شروع اپلیکیشن با وضعیت سایدبار --}}
    <div id="app" x-data="{ sidebarOpen: false }" class="flex flex-col min-h-screen">
        
        {{-- منوی کشویی سایدبار (Mobile Drawer) --}}
        <div x-show="sidebarOpen" x-cloak class="relative z-[1000] lg:hidden">
            <div class="fixed inset-0 bg-black/40 backdrop-blur-sm" @click="sidebarOpen = false"></div>
            <div class="fixed inset-y-0 right-0 w-72 bg-white shadow-2xl p-6 transition-transform"
                 x-show="sidebarOpen"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="translate-x-full"
                 x-transition:enter-end="translate-x-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="translate-x-0"
                 x-transition:leave-end="translate-x-full">
                
                <div class="flex justify-between items-center mb-8 border-b pb-4">
                    <span class="font-bold text-pink-600 text-lg">منوی دسترسی</span>
                    <button @click="sidebarOpen = false" class="text-gray-400 hover:text-gray-600">
                        <i class="fa fa-times text-2xl"></i>
                    </button>
                </div>

                <nav class="space-y-2">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-pink-50 transition text-gray-700">
                        <i class="fa fa-th-large text-pink-500"></i> <span>داشبورد</span>
                    </a>
                    <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-pink-50 transition text-gray-700">
                        <i class="fa fa-user-edit text-pink-500"></i> <span>ویرایش پروفایل</span>
                    </a>
                    <a href="{{ route('user.tickets.index') }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-pink-50 transition text-gray-700">
                        <i class="fa fa-ticket-alt text-pink-500"></i> <span>تیکت‌های پشتیبانی</span>
                    </a>
                    <a href="{{ route('search') }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-pink-50 transition text-gray-700 {{ request()->routeIs('search') ? 'bg-pink-50 text-pink-600 font-bold' : '' }}">
                         <i class="fa fa-search w-5 text-pink-500"></i> <span>جستجوی پیشرفته</span>
                    </a>
                    <hr class="my-4">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-3 p-3 text-red-500 hover:bg-red-50 rounded-xl transition">
                            <i class="fa fa-sign-out-alt"></i> <span>خروج از حساب</span>
                        </button>
                    </form>
                </nav>
            </div>
        </div>

        {{-- نوبار --}}
        <nav class="bg-white/80 backdrop-blur-md border-b sticky top-0 z-50 h-14 flex items-center">
            <div class="container mx-auto px-4 flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <button @click="sidebarOpen = true" class="text-gray-600 p-2 hover:bg-gray-100 rounded-lg lg:hidden">
                        <i class="fa fa-bars text-xl"></i>
                    </button>

                    <a href="{{ url('/') }}" class="flex items-center gap-2">
                        <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-8 w-8">
                        <span class="font-bold text-lg tracking-tight text-pink-600">DatingApp</span>
                    </a>
                </div>

                <div class="flex items-center gap-4">
                   <div class="relative" x-data="{ langMenu: false }">
                        <button @click="langMenu = !langMenu" class="text-gray-500 text-sm focus:outline-none p-2">
                            <i class="fa-solid fa-globe"></i>
                        </button>
                        
                        <div x-show="langMenu" @click.away="langMenu = false" x-cloak 
                             class="absolute {{ app()->getLocale() == 'fa' ? 'right-0' : 'left-0' }} mt-2 w-32 bg-white shadow-2xl border border-gray-100 rounded-xl overflow-hidden z-[100]">
                            <a href="{{ url('lang/fa') }}" class="flex items-center gap-2 px-4 py-3 text-sm text-gray-700 hover:bg-pink-50">🇮🇷 فارسی</a>
                            <a href="{{ url('lang/en') }}" class="flex items-center gap-2 px-4 py-3 text-sm text-gray-700 hover:bg-pink-50">🇬🇧 English</a>
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
                </a>
                <a href="{{ route('dashboard') }}" class="flex flex-col items-center justify-center w-full {{ request()->routeIs('dashboard') ? 'text-pink-600' : 'text-gray-400' }}">
                    <i class="fas fa-user-circle text-xl"></i>
                    <span class="text-[10px] mt-1">پروفایل</span>
                </a>
            </div>
        </nav>
    </div>

    <script>
        function hideGlobalLoader() {
            const loader = document.getElementById('globalLoading');
            if (loader) {
                loader.classList.add('loader-hidden');
                setTimeout(() => { loader.style.display = 'none'; }, 500);
            }
        }
        window.addEventListener('load', hideGlobalLoader);
        setTimeout(hideGlobalLoader, 3000);
    </script>

    @stack('scripts')
</body>
</html>