@extends('layouts.app')

@section('content')
    <!DOCTYPE html>
    <html lang="fa" dir="rtl">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Vazir:wght@400;700&display=swap" rel="stylesheet">
        <link rel="stylesheet" type="text/css"
            href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.6.0/slick.css" />
        <link rel="stylesheet" type="text/css"
            href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.6.0/slick-theme.css" />
        <style>
            body {
                font-family: 'Vazir', sans-serif;
                background: linear-gradient(to bottom, #f7fafc, #e2e8f0);
            }

            .sidebar {
                background: linear-gradient(180deg, #ff5e62 0%, #f6d365 100%);
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
                transition: transform 0.3s ease;
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
                transform: translateX(20px);
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
                    z-index: 50;
                }

                .sidebar-open {
                    transform: translateX(0);
                }

                .main-content {
                    margin-right: 0 !important;
                }
            }
        </style>
    </head>

    <body class="min-h-screen">
        <div class="flex">
            <!-- نوار کناری -->
            <aside class="sidebar w-64 h-screen fixed top-0 right-0 p-6 text-white flex flex-col md:sidebar-open"
                id="mySidebar">
                <div class="flex items-center space-x-3 space-x-reverse mb-8">
                    <img src="{{ asset('storage/' . (auth()->user()->profile_picture ?? 'default.jpg')) }}" alt="Profile"
                        class="w-12 h-12 rounded-full profile-img">
                    <div>
                        <span class="text-lg font-bold">سلام، {{ auth()->user()->name ?? 'کاربر' }}</span>
                        <div class="flex space-x-2 space-x-reverse mt-2">
                            <a href="{{ route('messages.index') }}" class="text-white hover:text-pink-200"><i
                                    class="fa fa-envelope"></i></a>
                            <a href="{{ route('profile.show', auth()->id()) }}" class="text-white hover:text-pink-200"><i
                                    class="fa fa-user"></i></a>
                            <a href="" class="text-white hover:text-pink-200"><i class="fa fa-cog"></i></a>
                        </div>
                    </div>
                </div>
                <nav class="space-y-2">
                    <a href="{{ route('dashboard') }}"
                        class="flex items-center space-x-2 space-x-reverse p-3 rounded-lg bg-pink-600 hover:bg-pink-700">
                        <i class="fa fa-users"></i>
                        <span>نمای کلی</span>
                    </a>
                    <a href="{{ route('profile.show', auth()->id()) }}"
                        class="flex items-center space-x-2 space-x-reverse p-3 rounded-lg hover:bg-pink-500">
                        <i class="fa fa-eye"></i>
                        <span>پروفایل</span>
                    </a>
                    <a href="{{ route('search') }}"
                        class="flex items-center space-x-2 space-x-reverse p-3 rounded-lg hover:bg-pink-500">
                        <i class="fa fa-search"></i>
                        <span>جستجو</span>
                    </a>
                    <a href="{{ route('profile.edit') }}"
                        class="flex items-center space-x-2 space-x-reverse p-3 rounded-lg hover:bg-pink-500">
                        <i class="fa fa-edit"></i>
                        <span>ویرایش پروفایل</span>
                    </a>
                    <a href="#" class="flex items-center space-x-2 space-x-reverse p-3 rounded-lg hover:bg-pink-500">
                        <i class="fa fa-diamond"></i>
                        <span>خرید</span>
                    </a>
                    <a href="#" class="flex items-center space-x-2 space-x-reverse p-3 rounded-lg hover:bg-pink-500">
                        <i class="fa fa-bell"></i>
                        <span>پشتیبانی</span>
                    </a>
                    <a href="#" class="flex items-center space-x-2 space-x-reverse p-3 rounded-lg hover:bg-pink-500">
                        <i class="fa fa-shield"></i>
                        <span>پلیس سایت</span>
                    </a>
                    <a href="#" class="flex items-center space-x-2 space-x-reverse p-3 rounded-lg hover:bg-pink-500">
                        <i class="fa fa-history"></i>
                        <span>تاریخچه</span>
                    </a>
                    <a href="#" class="flex items-center space-x-2 space-x-reverse p-3 rounded-lg hover:bg-pink-500">
                        <i class="fa fa-cog"></i>
                        <span>تنظیمات</span>
                    </a>
                </nav>
                <div class="mt-auto">
                    <a href="{{ route('logout') }}"
                        class="block p-3 rounded-lg bg-red-600 hover:bg-red-700 text-center">خروج</a>
                </div>
            </aside>

            <!-- محتوای اصلی -->
            <main class="main-content md:mr-64 p-6 w-full">
                <!-- دکمه منوی موبایل -->
                <button class="md:hidden text-gray-800 p-2 focus:outline-none" onclick="toggleSidebar()">
                    <i class="fa fa-bars text-2xl"></i>
                </button>

                <!-- هدر -->
                <header class="mb-8 animate-slide-in">
                    <h2 class="text-3xl font-bold text-gray-800"><i class="fa fa-dashboard mr-2"></i> داشبورد من</h2>
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
                                        <td class="py-3"><i class="fa fa-user text-blue-500 mr-2"></i> مشاهده جدید</td>
                                        <td class="py-3 text-gray-600">10 دقیقه قبل</td>
                                    </tr>
                                    <tr class="border-b">
                                        <td class="py-3"><i class="fa fa-bell text-red-500 mr-2"></i> هشدار سیستم</td>
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
                <!-- اعضای فعال -->
                <div class="animate-slide-in" style="animation-delay: 0.7s;">
                    <h3 class="text-2xl font-bold text-gray-800 mb-4">اعضای فعال</h3>
                    <div class="bg-white shadow-lg rounded-xl p-6">
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                            @foreach ($recentUsers as $recentUser)
                                <div class="flex flex-col items-center">
                                    <a href="{{ route('profile.show', $recentUser->id) }}">
                                        <img src="{{ asset('storage/' . ($recentUser->profile_picture ?? 'default.jpg')) }}"
                                            alt="{{ $recentUser->name ?? 'کاربر' }}"
                                            class="w-30 h-30 sm:w-24 sm:h-24 rounded-full object-cover mb-3 border-2 border-white shadow-lg hover:scale-110 transition-transform duration-300">
                                    </a>
                                    <span
                                        class="text-sm text-gray-600 text-center">{{ $recentUser->name ?? 'کاربر' }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <!-- نظرات اخیر -->
                <div class="mt-8 animate-slide-in" style="animation-delay: 0.8s;">
                    <h3 class="text-2xl font-bold text-gray-800 mb-4">نظرات اخیر</h3>
                    <div class="bg-white card p-6 rounded-xl">
                        <p class="text-gray-600 mb-2">John: کار عالی!</p>
                        <p class="text-gray-600">Ali: منتظرم برای آپدیت بعدی</p>
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

                <!-- فوتر -->
                <footer class="mt-8 bg-gray-100 p-6 rounded-xl text-center animate-slide-in" style="animation-delay: 1s;">
                    <p class="text-gray-600">ساخته شده با Laravel و Tailwind CSS</p>
                </footer>
            </main>
        </div>

        <div class="fixed inset-0 bg-black bg-opacity-50 hidden md:hidden" id="myOverlay" onclick="toggleSidebar()">
        </div>

        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.6.0/slick.min.js"></script>
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

            // انیمیشن برای اسلاید محتوا
            document.addEventListener('DOMContentLoaded', () => {
                document.querySelectorAll('.animate-slide-in').forEach((el, index) => {
                    el.style.animationDelay = `${index * 0.1}s`;
                });
            });
        </script>
    </body>

    </html>
@endsection
