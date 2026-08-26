<!doctype html>
<html
    lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    dir="{{ app()->getLocale() == 'fa' ? 'rtl' : 'ltr' }}"
>

<head>
    <meta charset="utf-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0"
    >
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'DatingApp') }}</title>

    @vite([
        'resources/css/app.css',
        'resources/js/app.js',
    ])

    <link
        href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}"
        rel="stylesheet"
    >

    @yield('head')

    <style>
        [x-cloak] {
            display: none !important;
        }

        body {
            font-family: Tahoma, sans-serif;
            -webkit-tap-highlight-color: transparent;
        }

        .pb-safe {
            padding-bottom: calc(4rem + env(safe-area-inset-bottom));
        }

        .loader-hidden {
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.5s ease-out;
        }
    </style>
</head>

<body class="bg-gray-50 text-gray-900">

    {{-- لودینگ --}}
    <div
        id="globalLoading"
        class="fixed inset-0 bg-white flex flex-col items-center justify-center z-[9999]"
    >
        <div class="relative">
            <div
                class="w-16 h-16 border-4 border-pink-100 border-t-pink-600 rounded-full animate-spin"
            ></div>

            <div class="absolute inset-0 flex items-center justify-center">
                <i class="fas fa-heart text-pink-500 animate-pulse"></i>
            </div>
        </div>

        <span class="mt-4 text-sm font-medium text-gray-500">
            در حال بارگذاری...
        </span>
    </div>

    {{-- شروع اپلیکیشن با وضعیت سایدبار --}}
    <div
        id="app"
        x-data="{ sidebarOpen: false }"
        class="flex flex-col min-h-screen"
    >

        {{-- منوی کشویی سایدبار --}}
        @auth
            <div
                x-show="sidebarOpen"
                x-cloak
                class="relative z-[1000] lg:hidden"
            >
                <div
                    class="fixed inset-0 bg-black/40 backdrop-blur-sm"
                    @click="sidebarOpen = false"
                ></div>

                <div
                    class="fixed inset-y-0 right-0 w-72 bg-white shadow-2xl p-6 transition-transform"
                    x-show="sidebarOpen"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="translate-x-full"
                    x-transition:enter-end="translate-x-0"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="translate-x-0"
                    x-transition:leave-end="translate-x-full"
                >
                    <div
                        class="flex flex-col items-center pb-4 mb-6 border-b border-gray-100 relative"
                    >
                        <button
                            type="button"
                            @click="sidebarOpen = false"
                            class="absolute top-0 left-0 text-gray-400 hover:text-gray-600 transition-colors"
                            aria-label="بستن منو"
                        >
                            <i class="fa fa-times text-xl"></i>
                        </button>

                        <div
                            class="w-20 h-20 rounded-full overflow-hidden border-2 border-pink-500 shadow-md mb-3 bg-gray-100 flex items-center justify-center"
                        >
                            @if(auth()->user()->profile_picture)
                                <img
                                    src="{{ asset('storage/' . auth()->user()->profile_picture) }}"
                                    alt="{{ auth()->user()->name }}"
                                    class="w-full h-full object-cover"
                                >
                            @else
                                <i class="fa fa-user text-3xl text-gray-400"></i>
                            @endif
                        </div>

                        <span class="font-bold text-gray-800 text-base">
                            {{ auth()->user()->name }}
                        </span>
                    </div>

                    <nav class="space-y-2">
                        <a
                            href="{{ route('dashboard') }}"
                            class="flex items-center gap-3 p-3 rounded-xl hover:bg-pink-50 transition text-gray-700"
                        >
                            <i class="fa fa-th-large text-pink-500"></i>
                            <span>داشبورد</span>
                        </a>

                        <a
                            href="{{ route('messages.index') }}"
                            class="flex items-center gap-3 p-3 rounded-xl bg-white/10 hover:bg-white/20 transition text-gray-700"
                        >
                            <i class="fa fa-envelope text-pink-500"></i>
                            <span>پیام‌های من</span>
                        </a>

                        <a
                            href="{{ route('likes.index') }}"
                            class="flex items-center gap-3 p-3 rounded-xl bg-white/10 hover:bg-white/20 transition text-gray-700"
                        >
                            <i class="fa fa-heart text-pink-500"></i>
                            <span>لیست لایک‌ها</span>
                        </a>

                        <a
                            href="{{ route('profile.edit') }}"
                            class="flex items-center gap-3 p-3 rounded-xl hover:bg-pink-50 transition text-gray-700"
                        >
                            <i class="fa fa-user-edit text-pink-500"></i>
                            <span>ویرایش پروفایل</span>
                        </a>

                        <a
                            href="{{ route('user.tickets.index') }}"
                            class="flex items-center gap-3 p-3 rounded-xl hover:bg-pink-50 transition text-gray-700"
                        >
                            <i class="fa fa-ticket-alt text-pink-500"></i>
                            <span>تیکت‌های پشتیبانی</span>
                        </a>

                        <a
                            href="{{ route('search') }}"
                            class="flex items-center gap-3 p-3 rounded-xl hover:bg-pink-50 transition text-gray-700 {{ request()->routeIs('search') ? 'bg-pink-50 text-pink-600 font-bold' : '' }}"
                        >
                            <i class="fa fa-search w-5 text-pink-500"></i>
                            <span>جستجوی پیشرفته</span>
                        </a>

                        <a
                            href="{{ route('premium.upgrade') }}"
                            class="flex items-center gap-3 p-3 rounded-xl bg-amber-500/10 hover:bg-amber-500/20 border border-amber-500/30 transition text-amber-600"
                        >
                            <i class="fa fa-crown text-amber-500"></i>
                            <span class="font-bold">ارتقا به ویژه</span>
                        </a>

                        <hr class="my-4 border-gray-100">

                        <form action="{{ route('logout') }}" method="POST">
                            @csrf

                            <button
                                type="submit"
                                class="w-full flex items-center gap-3 p-3 text-red-500 hover:bg-red-50 rounded-xl transition"
                            >
                                <i class="fa fa-sign-out-alt"></i>
                                <span>خروج از حساب</span>
                            </button>
                        </form>
                    </nav>
                </div>
            </div>
        @endauth

        {{-- نوار بالای سایت --}}
        <nav
            class="bg-white/80 backdrop-blur-md border-b sticky top-0 z-50 h-14 flex items-center"
        >
            <div
                class="container mx-auto px-4 flex justify-between items-center"
            >
                {{-- لوگو و منوی همبرگری --}}
                <div class="flex items-center gap-3">
                    @auth
                        <button
                            type="button"
                            @click="sidebarOpen = true"
                            class="text-gray-600 p-2 hover:bg-gray-100 rounded-lg lg:hidden"
                            aria-label="باز کردن منو"
                        >
                            <i class="fa fa-bars text-xl"></i>
                        </button>
                    @endauth

                    <a
                        href="{{ url('/') }}"
                        class="flex items-center gap-2"
                    >
                        <img
                            src="{{ asset('images/logo.png') }}"
                            alt="Logo"
                            class="h-8 w-8"
                        >

                        <span
                            class="font-bold text-lg tracking-tight text-pink-600"
                        >
                            vlora
                        </span>
                    </a>
                </div>

                {{-- بازگشت و انتخاب زبان --}}
                <div class="flex items-center gap-2">
                    @if(!request()->is('/') && !request()->routeIs('home'))
                        <button
                            type="button"
                            data-history-back
                            class="text-gray-600 p-2 hover:bg-gray-100 rounded-lg transition-colors flex items-center justify-center"
                            title="بازگشت"
                            aria-label="بازگشت"
                        >
                            <i class="fa fa-arrow-left text-lg"></i>
                        </button>
                    @endif

                    <div
                        class="relative"
                        x-data="{ langMenu: false }"
                    >
                        <button
                            type="button"
                            @click="langMenu = !langMenu"
                            class="text-gray-500 text-sm focus:outline-none p-2 flex items-center justify-center"
                            aria-label="انتخاب زبان"
                        >
                            <i class="fa-solid fa-globe text-lg"></i>
                        </button>

                        <div
                            x-show="langMenu"
                            @click.away="langMenu = false"
                            x-cloak
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="transform opacity-0 scale-95"
                            x-transition:enter-end="transform opacity-100 scale-100"
                            class="absolute {{ app()->getLocale() == 'fa' ? 'left-0' : 'right-0' }} mt-2 w-32 bg-white shadow-2xl border border-gray-100 rounded-xl overflow-hidden z-[100]"
                        >
                            <a
                                href="{{ url('lang/fa') }}"
                                class="flex items-center gap-2 px-4 py-3 text-sm text-gray-700 hover:bg-pink-50 transition-colors"
                            >
                                <span class="text-base">🇮🇷</span>
                                <span>فارسی</span>
                            </a>

                            <a
                                href="{{ url('lang/en') }}"
                                class="flex items-center gap-2 px-4 py-3 text-sm text-gray-700 hover:bg-pink-50 transition-colors"
                            >
                                <span class="text-base">🇬🇧</span>
                                <span>English</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        {{-- محتوای اصلی --}}
        <main class="main-content w-full flex-1 p-4 md:p-8">
            <div class="max-w-md mx-auto">
                @yield('content')
            </div>
        </main>

        {{-- منوی پایین موبایل --}}
        @auth
            <nav
                class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 px-2 py-1 z-50 sm:hidden"
            >
                <div class="flex justify-around items-center h-14">
                    <a
                        href="{{ url('/') }}"
                        class="flex flex-col items-center justify-center w-full {{ request()->is('/') ? 'text-pink-600' : 'text-gray-400' }}"
                    >
                        <i class="fas fa-heart text-xl"></i>
                        <span class="text-[10px] mt-1">اکتشاف</span>
                    </a>

                    <a
                        href="{{ route('search') }}"
                        class="flex flex-col items-center justify-center w-full {{ request()->routeIs('search') ? 'text-pink-600' : 'text-gray-400' }}"
                    >
                        <i class="fas fa-search text-xl"></i>
                        <span class="text-[10px] mt-1">جستجو</span>
                    </a>

                    <a
                        href="{{ route('messages.index') }}"
                        class="flex flex-col items-center justify-center w-full relative {{ request()->routeIs('messages.*') ? 'text-pink-600' : 'text-gray-400' }}"
                    >
                        <i class="fas fa-comment-dots text-xl"></i>
                        <span class="text-[10px] mt-1">پیام‌ها</span>
                    </a>

                    <a
                        href="{{ route('dashboard') }}"
                        class="flex flex-col items-center justify-center w-full {{ request()->routeIs('dashboard') ? 'text-pink-600' : 'text-gray-400' }}"
                    >
                        <i class="fas fa-user-circle text-xl"></i>
                        <span class="text-[10px] mt-1">پروفایل</span>
                    </a>
                </div>
            </nav>
        @endauth
    </div>

    @stack('scripts')
</body>
</html>