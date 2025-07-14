```blade
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
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
        }

        .btn-primary {
            transition: transform 0.3s ease, background-color 0.3s ease;
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

            [x-show="openLogoutModal"]~.main-content,
            [x-show="openLogoutModal"]~header,
            [x-show="openLogoutModal"]~aside,
            [x-show="openLogoutModal"]~footer {
                pointer-events: none;
            }
        }

        #myOverlay {
            z-index: 9997;
        }

        /* اصلاح جهت اسلایدر برای RTL */
        .slick-slider {
            direction: rtl;
        }
    </style>

    <div class="min-h-screen" x-data="{ openLogoutModal: false }">
        <div class="flex">
            <!-- نوار کناری -->
            <aside class="sidebar w-64 h-screen fixed top-0 right-0 p-6 text-white flex flex-col md:sidebar-open"
                id="mySidebar">
                <div class="flex items-center space-x-reverse space-x-3 mb-8">
                    <img src="{{ asset('storage/' . (auth()->user()->profile_picture ?? 'default.jpg')) }}" alt="Profile"
                        class="w-12 h-12 rounded-full profile-img">
                    <div>
                        <span class="text-lg font-bold">سلام، {{ auth()->user()->name ?? 'کاربر' }}</span>
                        <div class="flex space-x-reverse space-x-2 mt-2">
                            <a href="{{ route('messages.index') }}" class="text-white hover:text-pink-200"><i
                                    class="fa fa-envelope"></i></a>
                            <a href="{{ route('profile.show', auth()->id()) }}" class="text-white hover:text-pink-200"><i
                                    class="fa fa-user"></i></a>
                            <a href="#" class="text-white hover:text-pink-200"><i class="fa fa-cog"></i></a>
                        </div>
                    </div>
                </div>
                <nav class="space-y-2">
                    <a href="{{ route('dashboard') }}"
                        class="flex items-center space-x-reverse space-x-2 p-3 rounded-lg bg-pink-600 hover:bg-pink-700">
                        <i class="fa fa-users"></i><span>نمای کلی</span>
                    </a>
                    <a href="{{ route('profile.show', auth()->id()) }}"
                        class="flex items-center space-x-reverse space-x-2 p-3 rounded-lg hover:bg-pink-500">
                        <i class="fa fa-eye"></i><span>پروفایل</span>
                    </a>
                    <a href="{{ route('search') }}"
                        class="flex items-center space-x-reverse space-x-2 p-3 rounded-lg hover:bg-pink-500">
                        <i class="fa fa-search"></i><span>جستجو</span>
                    </a>
                    <a href="{{ route('profile.edit') }}"
                        class="flex items-center space-x-reverse space-x-2 p-3 rounded-lg hover:bg-pink-500">
                        <i class="fa fa-edit"></i><span>ویرایش پروفایل</span>
                    </a>
                    <a href="#" class="flex items-center space-x-reverse space-x-2 p-3 rounded-lg hover:bg-pink-500">
                        <i class="fa fa-diamond"></i><span>خرید</span>
                    </a>
                    <a href="#" class="flex items-center space-x-reverse space-x-2 p-3 rounded-lg hover:bg-pink-500">
                        <i class="fa fa-bell"></i><span>پشتیبانی</span>
                    </a>
                    <a href="#" class="flex items-center space-x-reverse space-x-2 p-3 rounded-lg hover:bg-pink-500">
                        <i class="fa fa-shield"></i><span>پلیس سایت</span>
                    </a>
                    <a href="#" class="flex items-center space-x-reverse space-x-2 p-3 rounded-lg hover:bg-pink-500">
                        <i class="fa fa-history"></i><span>تاریخچه</span>
                    </a>
                    <a href="#" class="flex items-center space-x-reverse space-x-2 p-3 rounded-lg hover:bg-pink-500">
                        <i class="fa fa-cog"></i><span>تنظیمات</span>
                    </a>
                </nav>
                <button @click="openLogoutModal = true; console.log('Modal opened', openLogoutModal)"
                    class="w-full flex items-center justify-center gap-2 p-3 rounded-lg bg-red-600 hover:bg-red-700 text-white transition duration-300">
                    <i class="fa fa-sign-out"></i><span>خروج</span>
                </button>
            </aside>

            <!-- محتوای اصلی -->
            <main class="main-content md:mr-64 md:ml-0 p-6 w-full">
                <button class="md:hidden text-gray-800 p-2 focus:outline-none" onclick="toggleSidebar()">
                    <i class="fa fa-bars text-2xl"></i>
                </button>
                <header class="mb-8 animate-slide-in">
                    <h2 class="text-3xl font-bold text-gray-800"><i class="fa fa-dashboard ml-2"></i> داشبورد من</h2>
                </header>

                <!-- باکس‌های اطلاعات -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
                    <a href="{{ route('messages.index') }}"
                        class="card bg-gradient-to-br from-red-500 to-pink-500 text-white p-6 rounded-xl animate-slide-in"
                        style="animation-delay: 0.1s;">
                        <div class="flex items-center justify-between">
                            <i class="fa fa-comment text-4xl"></i>
                            <h3 class="text-2xl font-bold">{{ auth()->user()->unreadMessagesCount() }}</h3>
                        </div>
                        <h4 class="mt-2 text-lg">پیام جدید</h4>
                    </a>
                    <a href="{{ route('profile.edit') }}"
                        class="card bg-gradient-to-br from-blue-500 to-indigo-500 text-white p-6 rounded-xl animate-slide-in"
                        style="animation-delay: 0.2s;">
                        <div class="flex items-center justify-between">
                            <i class="fa fa-user text-4xl"></i>
                            <h3 class="text-2xl font-bold">ویرایش</h3>
                        </div>
                        <h4 class="mt-2 text-lg">پروفایل</h4>
                    </a>
                    <div class="card bg-gradient-to-br from-teal-500 to-cyan-500 text-white p-6 rounded-xl animate-slide-in"
                        style="animation-delay: 0.3s;">
                        <div class="flex items-center justify-between">
                            <i class="fa fa-credit-card text-4xl"></i>
                            <h3 class="text-2xl font-bold">23</h3>
                        </div>
                        <h4 class="mt-2 text-lg">پرداخت شارژ</h4>
                    </div>
                    <div class="card bg-gradient-to-br from-orange-500 to-yellow-500 text-white p-6 rounded-xl animate-slide-in"
                        style="animation-delay: 0.4s;">
                        <div class="flex items-center justify-between">
                            <i class="fa fa-heart text-4xl"></i>
                            <h3 class="text-2xl font-bold">50</h3>
                        </div>
                        <h4 class="mt-2 text-lg">افراد مچ‌شده</h4>
                    </div>
                </div>

                <!-- فعالیت‌ها و نوتیفیکیشن‌ها -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-12">
                    <div class="lg:col-span-2 animate-slide-in" style="animation-delay: 0.5s;">
                        <h3 class="text-2xl font-bold text-gray-800 mb-4">فعالیت‌های اخیر</h3>
                        <div class="bg-white card p-6 rounded-xl">
                            <table class="w-full text-right">
                                <tbody>
                                    <tr class="border-b">
                                        <td class="py-3"><i class="fa fa-user text-blue-500 ml-2"></i> مشاهده جدید</td>
                                        <td class="py-3 text-gray-600">10 دقیقه قبل</td>
                                    </tr>
                                    <tr class="border-b">
                                        <td class="py-3"><i class="fa fa-bell text-red-500 ml-2"></i> هشدار سیستم</td>
                                        <td class="py-3 text-gray-600">15 دقیقه قبل</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="animate-slide-in" style="animation-delay: 0.6s;">
                        <h3 class="text-2xl font-bold text-gray-800 mb-4">مناطق</h3>
                        <div class="bg-white card p-6 rounded-xl">
                            <table class="w-full text-right">
                                <tbody>
                                    <tr class="border-b">
                                        <td class="py-3">آمریکا</td>
                                        <td class="py-3 text-gray-600">65%</td>
                                    </tr>
                                    <tr>
                                        <td class="py-3">انگلستان</td>
                                        <td class="py-3 text-gray-600">15%</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- اعضای فعال -->
                <div class="animate-slide-in z-0" style="animation-delay: 0.7s;">
                    <h3 class="text-2xl font-bold text-gray-800 mb-4">اعضای فعال</h3>
                    <div class="bg-white shadow-lg rounded-xl p-6 customer-logos">
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                            @foreach ($recentUsers as $recentUser)
                                <div class="flex flex-col items-center">
                                    <a href="{{ route('profile.show', $recentUser->id) }}">
                                        <img src="{{ asset('storage/' . ($recentUser->profile_picture ?? 'default.jpg')) }}"
                                            alt="{{ $recentUser->name ?? 'کاربر' }}"
                                            class="w-24 h-24 rounded-full object-cover mb-3 border-2 border-white shadow-lg hover:scale-110 transition-transform duration-300">
                                    </a>
                                    <span
                                        class="text-sm text-gray-600 text-center">{{ $recentUser->name ?? 'کاربر' }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- بازدیدکنندگان اخیر -->
                <div class="mt-8 animate-slide-in z-0" style="animation-delay: 1.0s;">
                    <h3 class="text-2xl font-bold text-gray-800 mb-4">بازدیدکنندگان اخیر</h3>
                    <div class="bg-white card p-6 rounded-xl">
                        @forelse($recentProfileViews as $view)
                            <div class="flex items-center space-x-reverse space-x-4 border-b py-2">
                                <img src="{{ asset('storage/' . ($view->viewer->profile_picture ?? 'default.jpg')) }}"
                                    class="w-10 h-10 rounded-full" alt="{{ $view->viewer->name }}">
                                <div>
                                    <a href="{{ route('profile.show', $view->viewer->id) }}"
                                        class="text-pink-600 hover:underline font-bold">
                                        {{ $view->viewer->name }}
                                    </a>
                                    <p class="text-sm text-gray-500">{{ $view->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500">هنوز کسی پروفایل شما را مشاهده نکرده است.</p>
                        @endforelse
                    </div>
                </div>

                <!-- آمار پایینی -->
                <div class="mt-8 bg-gray-800 text-white p-6 rounded-xl animate-slide-in" style="animation-delay: 0.9s;">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <h4 class="text-lg font-bold">جمعیت</h4>
                            <p class="text-gray-300">کشور / شهر</p>
                        </div>
                        <div>
                            <h4 class="text-lg font-bold">سیستم</h4>
                            <p class="text-gray-300">مرورگر / سیستم‌عامل</p>
                        </div>
                        <div>
                            <h4 class="text-lg font-bold">هدف</h4>
                            <p class="text-gray-300">علایق کاربران</p>
                        </div>
                    </div>
                </div>

                <footer class="mt-8 bg-gray-100 p-6 rounded-xl text-center animate-slide-in" style="animation-delay: 1s;">
                    <p class="text-gray-600">ساخته شده با Laravel و Tailwind CSS</p>
                </footer>
            </main>
        </div>

        <!-- مودال تأیید -->
        <div x-show="openLogoutModal" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0" @keydown.escape.window="openLogoutModal = false"
            class="fixed inset-0 z-[9999] bg-black bg-opacity-50 flex items-center justify-center">
            <div @click.outside="openLogoutModal = false" class="bg-white rounded-lg p-6 w-72 text-center shadow-2xl">
                <h2 class="text-lg font-bold text-gray-800 mb-4">آیا مطمئن هستید؟</h2>
                <p class="text-gray-600 mb-6">می‌خواهید از حساب کاربری خود خارج شوید؟</p>
                <div class="flex justify-between">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">خروج</button>
                    </form>
                    <button @click="openLogoutModal = false" type="button"
                        class="bg-gray-300 text-gray-800 px-4 py-2 rounded hover:bg-gray-400">انصراف</button>
                </div>
            </div>
        </div>
    </div>

    <div class="fixed inset-0 bg-black bg-opacity-50 hidden md:hidden" id="myOverlay" onclick="toggleSidebar()"></div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.6.0/slick.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script>
        $(document).ready(function() {
            $('.customer-logos').slick({
                slidesToShow: 4,
                slidesToScroll: 1,
                autoplay: true,
                autoplaySpeed: 2000,
                arrows: false,
                dots: true,
                pauseOnHover: true,
                rtl: true,
                responsive: [{
                    breakpoint: 768,
                    settings: {
                        slidesToShow: 3
                    }
                }, {
                    breakpoint: 520,
                    settings: {
                        slidesToShow: 2
                    }
                }]
            });
        });

        function toggleSidebar() {
            const sidebar = document.getElementById('mySidebar');
            const overlay = document.getElementById('myOverlay');
            sidebar.classList.toggle('sidebar-open');
            overlay.classList.toggle('hidden');
        }

        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.animate-slide-in').forEach((el, index) => {
                el.style.animationDelay = `${index * 0.1}s`;
            });
        });
    </script>
@endsection
```
