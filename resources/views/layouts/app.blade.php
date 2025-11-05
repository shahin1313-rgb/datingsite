<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/alpinejs" defer></script>
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">


    @yield('head')

    <style>
          [x-cloak] { display: none !important; }  /* اضافه‌شده برای مخفی‌کردن منو هنگام بارگذاری */
        html,
        body,
        h1,
        h2,
        h3,
        h4,
        h5 {
            font-family: 'Raleway', sans-serif;
        }

        #loadingSpinner {
            border: 16px solid #f3f3f3;
            border-top: 16px solid #3498db;
            border-radius: 50%;
            width: 120px;
            height: 120px;
            animation: spin 2s linear infinite;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 1000;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>
</head>

<body class="bg-gray-100 min-h-screen">
    <div id="app">
        <nav class="bg-white shadow">
            <div class="container mx-auto px-4 py-4 flex justify-between items-center">
                <a href="{{ url('/') }}" class="text-xl font-bold text-gray-800">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-8 w-8">
                </a>
                <div class="space-x-4" x-data="{ open: false }">

                <!-- پیام‌ها -->
<a href="{{ route('messages.index') }}" class="relative inline-flex items-center text-gray-600 hover:text-gray-800">
    <i class="fas fa-envelope text-xl"></i>
    <span
        class="absolute -top-1 -right-2 bg-red-500 text-white text-xs rounded-full px-1.5 py-0.5">
        3
    </span>
</a>

                    @guest
                        @if (Route::has('login'))
                            <a href="{{ route('login') }}" class="text-gray-600 hover:text-gray-800">{{ __('Login') }}</a>
                        @endif
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}"
                                class="text-gray-600 hover:text-gray-800">{{ __('Register') }}</a>
                        @endif
                    @else
                        <div class="relative inline-block text-left" @click.away="open = false">
                            <button @click="open = !open"
                                class="inline-flex items-center text-gray-600 hover:text-gray-800">
                                {{ Auth::user()->name }}
                                <svg class="w-4 h-4 ml-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M5.23 7.21a.75.75 0 011.06.02L10 10.939l3.71-3.71a.75.75 0 011.06 1.061l-4.24 4.243a.75.75 0 01-1.06 0L5.25 8.28a.75.75 0 01-.02-1.06z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>
                            <div x-show="open" x-cloak class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-50">
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Buy</a>
                                <a href="{{ route('profile.edit') }}"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">ویرایش پروفایل</a>
                                <a href="{{ route('dashboard') }}"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Dashboard</a>
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">لیست
                                    علاقمندی‌ها</a>
                                <div class="border-t border-gray-200"></div>
                                <a href="{{ route('logout') }}"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    {{ __('Logout') }}
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                                    @csrf
                                </form>
                            </div>
                        </div>
                    @endguest
                </div>
            </div>
        </nav>

        <main class="p-4">
            <!-- <div id="loadingSpinner" role="status"></div> -->
            <div id="mainContent" class="bg-white p-4 rounded shadow">
                @yield('content')
            </div>
        </main>
    </div>
</body>

</html>
