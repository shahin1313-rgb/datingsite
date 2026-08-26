@extends('layouts.app')

@section('content')
    <style>
        * {
            font-family: 'Vazir', sans-serif !important;
        }

        html,
        body {
            direction: rtl;
            background: linear-gradient(to bottom, #f7fafc, #e2e8f0);
            text-align: right;
        }

        .sidebar {
            background: linear-gradient(180deg, #ff5e62 0%, #f6d365 100%);
            box-shadow: -4px 0 20px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
            z-index: 50;
        }

        .fixed.inset-0.bg-black.bg-opacity-50 {
            z-index: 9998;
        }

        [x-show="openLogoutModal"] {
            z-index: 9999;
        }

        .main-content,
        header,
        footer {
            z-index: 1;
        }

        .sidebar-hidden {
            transform: translateX(100%);
        }

        .card {
            transition:
                transform 0.3s ease,
                box-shadow 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
        }

        .btn-primary {
            transition:
                transform 0.3s ease,
                background-color 0.3s ease;
        }

        .btn-primary:hover {
            transform: scale(1.05);
            background-color: #fefcbf;
        }

        .profile-img {
            border: 4px solid #fff;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .animate-slide-in {
            opacity: 0;
            transform: translateX(-20px);
            animation: slideIn 0.5s forwards;
        }

        @keyframes slideIn {
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .slick-slide img {
            border-radius: 50%;
            width: 80px;
            height: 80px;
            object-fit: cover;
            margin: 0 auto;
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(100%);
                position: fixed;
                top: 0;
                right: 0;
                width: 260px;
            }

            .sidebar-open {
                transform: translateX(0);
            }

            .main-content {
                margin-right: 0 !important;
                margin-left: 0 !important;
            }

            [x-show="openLogoutModal"] {
                pointer-events: auto;
            }

            .fixed.inset-0.bg-black.bg-opacity-50 {
                pointer-events: auto;
            }

            .main-content,
            header,
            aside,
            footer {
                pointer-events: auto;
            }

            [x-show="openLogoutModal"] ~ .main-content,
            [x-show="openLogoutModal"] ~ header,
            [x-show="openLogoutModal"] ~ aside,
            [x-show="openLogoutModal"] ~ footer {
                pointer-events: none;
            }
        }

        #myOverlay {
            z-index: 9997;
        }

        .slick-slider {
            direction: rtl;
        }
    </style>

    <div
        class="min-h-screen"
        x-data="{ openLogoutModal: false }"
    >
        <div class="flex">

            <aside
                class="sidebar w-64 h-screen fixed top-0 right-0 p-6 text-white flex flex-col md:sidebar-open"
                id="mySidebar"
            >
                <div
                    class="flex items-center space-x-reverse space-x-3 mb-8"
                >
                    <img
                        src="{{ auth()->user()->profilePhotoUrl() }}"
                        alt="Profile"
                        class="w-12 h-12 rounded-full profile-img"
                    >

                    <div>
                        <span class="text-lg font-bold">
                            سلام، {{ auth()->user()->name ?? 'کاربر' }}
                        </span>

                        <div
                            class="flex space-x-reverse space-x-2 mt-2"
                        >
                            <a
                                href="{{ route('messages.index') }}"
                                class="text-white hover:text-pink-200"
                            >
                                <i class="fa fa-envelope"></i>
                            </a>

                            <a
                                href="{{ route('profile.show', auth()->id()) }}"
                                class="text-white hover:text-pink-200"
                            >
                                <i class="fa fa-user"></i>
                            </a>

                            <a
                                href="#"
                                class="text-white hover:text-pink-200"
                            >
                                <i class="fa fa-cog"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <nav class="space-y-2">
                    <a
                        href="{{ route('dashboard') }}"
                        class="flex items-center space-x-reverse space-x-2 p-3 rounded-lg bg-pink-600 hover:bg-pink-700"
                    >
                        <i class="fa fa-users"></i>
                        <span>نمای کلی</span>
                    </a>

                    <a
                        href="{{ route('profile.show', auth()->id()) }}"
                        class="flex items-center space-x-reverse space-x-2 p-3 rounded-lg hover:bg-pink-500"
                    >
                        <i class="fa fa-eye"></i>
                        <span>پروفایل</span>
                    </a>

                    <a
                        href="{{ route('search') }}"
                        class="flex items-center space-x-reverse space-x-2 p-3 rounded-lg hover:bg-pink-500"
                    >
                        <i class="fa fa-search"></i>
                        <span>جستجو</span>
                    </a>

                    <a
                        href="{{ route('profile.edit') }}"
                        class="flex items-center space-x-reverse space-x-2 p-3 rounded-lg hover:bg-pink-500"
                    >
                        <i class="fa fa-edit"></i>
                        <span>ویرایش پروفایل</span>
                    </a>

                    <a
                        href="#"
                        class="flex items-center space-x-reverse space-x-2 p-3 rounded-lg hover:bg-pink-500"
                    >
                        <i class="fa fa-diamond"></i>
                        <span>خرید</span>
                    </a>

                    <a
                        href="{{ route('user.tickets.index') }}"
                        class="block py-2 px-4 hover:bg-gray-100"
                    >
                        📨 تیکت‌های پشتیبانی
                    </a>

                    <a
                        href="{{ route('police.index') }}"
                        class="flex items-center space-x-reverse space-x-2 p-3 rounded-lg hover:bg-pink-500"
                    >
                        <i class="fa fa-shield"></i>
                        <span>پلیس سایت</span>
                    </a>

                    <a
                        href="#"
                        class="flex items-center space-x-reverse space-x-2 p-3 rounded-lg hover:bg-pink-500"
                    >
                        <i class="fa fa-history"></i>
                        <span>تاریخچه</span>
                    </a>

                    <a
                        href="#"
                        class="flex items-center space-x-reverse space-x-2 p-3 rounded-lg hover:bg-pink-500"
                    >
                        <i class="fa fa-cog"></i>
                        <span>تنظیمات</span>
                    </a>
                </nav>

                <button
                    type="button"
                    @click="openLogoutModal = true"
                    class="w-full flex items-center justify-center gap-2 p-3 rounded-lg bg-red-600 hover:bg-red-700 text-white transition duration-300"
                >
                    <i class="fa fa-sign-out"></i>
                    <span>خروج</span>
                </button>
            </aside>

            @include('police.list')
        </div>

        <div
            x-show="openLogoutModal"
            x-cloak
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @keydown.escape.window="openLogoutModal = false"
            class="fixed inset-0 z-[9999] bg-black bg-opacity-50 flex items-center justify-center"
        >
            <div
                @click.outside="openLogoutModal = false"
                class="bg-white rounded-lg p-6 w-72 text-center shadow-2xl"
            >
                <h2 class="text-lg font-bold text-gray-800 mb-4">
                    آیا مطمئن هستید؟
                </h2>

                <p class="text-gray-600 mb-6">
                    می‌خواهید از حساب کاربری خود خارج شوید؟
                </p>

                <div class="flex justify-between">
                    <form
                        method="POST"
                        action="{{ route('logout') }}"
                    >
                        @csrf

                        <button
                            type="submit"
                            class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700"
                        >
                            خروج
                        </button>
                    </form>

                    <button
                        type="button"
                        @click="openLogoutModal = false"
                        class="bg-gray-300 text-gray-800 px-4 py-2 rounded hover:bg-gray-400"
                    >
                        انصراف
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div
        class="fixed inset-0 bg-black bg-opacity-50 hidden md:hidden"
        id="myOverlay"
        data-toggle-sidebar
    ></div>

    <script nonce="{{ \Illuminate\Support\Facades\Vite::cspNonce() }}">
        document.addEventListener('DOMContentLoaded', () => {
            document
                .querySelectorAll('.animate-slide-in')
                .forEach((element, index) => {
                    element.style.animationDelay =
                        `${index * 0.1}s`;
                });
        });
    </script>
@endsection
