@extends('layouts.app')

@section('content')
<style>
    /* جلوگیری از پرش المان‌ها قبل از لود جاوااسکریپت */
    [x-cloak] { display: none !important; }

    body {
        font-family: 'Vazir', sans-serif !important;
        background-color: #f4f7f6;
        margin: 0;
        overflow-x: hidden;
    }

    /* سایدبار مدرن */
    .sidebar {
        background: linear-gradient(135deg, #ff5e62 0%, #ff9966 100%);
        width: 260px;
        z-index: 40; /* لایه متوسط */
        transition: transform 0.3s ease;
    }

    /* استایل مودال خروج */
    .modal-backdrop {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(4px);
        z-index: 9999; /* بالاترین لایه */
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1rem;
    }

    @media (max-width: 768px) {
        .sidebar {
            position: fixed;
            right: 0;
            top: 0;
            height: 100vh;
            transform: translateX(100%);
        }
        .sidebar.sidebar-open {
            transform: translateX(0);
        }
    }
</style>
<main class="main-content w-full flex-1 p-4 md:p-8">
    
        <div x-data="{ openLogoutModal: false, sidebarOpen: false }" class="relative min-h-screen flex flex-row-reverse">
            
            <aside :class="sidebarOpen ? 'sidebar-open' : ''" 
            class="sidebar sticky top-0 h-screen w-[260px] min-w-[260px] flex-shrink-0 flex flex-col p-5 text-white transform-none md:transform-none">
            
                <div class="flex flex-col items-center py-6 mb-6">
                    <div class="w-16 h-16 rounded-full border-2 border-white/50 overflow-hidden shadow-lg">
                        <img src="{{ asset('storage/' . (auth()->user()->profile_picture ?? 'default.jpg')) }}" class="w-full h-full object-cover">
                    </div>
                    <span class="mt-3 font-bold text-sm">{{ auth()->user()->name }}</span>
                </div>

            <nav class="space-y-2 flex-1 overflow-y-auto">
                
                <a href="{{ route('dashboard') }}" class="flex items-center justify-start gap-3 p-3 rounded-xl bg-white/10 hover:bg-white/20 transition">
                    <i class="fa fa-th-large w-5"></i> <span>داشبورد</span>
                </a>

                <a href="{{ route('profile.edit') }}" class="flex items-center justify-start gap-3 p-3 rounded-xl bg-white/10 hover:bg-white/20 transition">
                    <i class="fa fa-user-edit w-5"></i> <span>ویرایش پروفایل</span>
                </a>

                <a href="{{ route('messages.index') }}" class="flex items-center justify-start gap-3 p-3 rounded-xl bg-white/10 hover:bg-white/20 transition">
                    <i class="fa fa-envelope w-5"></i> <span>پیام‌های من</span>
                </a>

                <a href="{{ route('user.tickets.index') }}" class="flex items-center justify-start gap-3 p-3 rounded-xl bg-white/10 hover:bg-white/20 transition">
                    <i class="fa fa-ticket-alt w-5"></i> <span>تیکت‌های پشتیبانی</span>
                </a>

                <a href="{{ route('likes.index') }}" class="flex items-center justify-start gap-3 p-3 rounded-xl bg-white/10 hover:bg-white/20 transition">
                    <i class="fa fa-heart w-5"></i> <span>لیست لایک‌ها</span>
                </a>

                <a href="{{ route('police.index') }}" class="flex items-center justify-start gap-3 p-3 rounded-xl bg-white/10 hover:bg-white/20 transition">
                    <i class="fa fa-user-slash w-5"></i> <span>لیست سیاه</span>
                </a>

                <a href="{{ route('premium.upgrade') }}" class="flex items-center justify-start gap-3 p-3 rounded-xl bg-amber-500/20 hover:bg-amber-500/30 border border-amber-500/50 transition">
                    <i class="fa fa-crown text-amber-400 w-5"></i> <span class="text-amber-400 font-bold">ارتقا به ویژه</span>
                </a>

                <form action="{{ route('logout') }}" method="POST" class="w-full">
                    @csrf
                    <button type="submit" class="w-full flex items-center justify-start gap-3 p-3 rounded-xl bg-red-500/10 hover:bg-red-500/20 text-red-400 transition">
                        <i class="fa fa-sign-out-alt w-5"></i> <span>خروج</span>
                    </button>
                </form>

                <button type="button" @click="openLogoutModal = true" 
                    class="w-full bg-white/10 hover:bg-red-500 p-3 rounded-xl transition flex items-center justify-start gap-3 border border-white/20 mt-auto">
                    <i class="fa fa-sign-out w-5"></i> <span>خروج سریع</span>
                </button>
            </nav>
            </aside>
            <div class="flex-1 flex flex-col min-w-0 bg-gray-50">
                
                <div class="animate-slide-in">
                    <div class="relative bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden mb-8">
                        <div class="h-32 bg-gradient-to-r from-pink-500 to-orange-400"></div>
                        
                        <div class="px-6 pb-6">
                            <div class="relative flex flex-col md:flex-row items-center md:items-end -mt-16 md:-mt-12 gap-5">
                                <div class="relative group">
                                    <img src="{{ asset('storage/' . (auth()->user()->profile_picture ?? 'default.png')) }}" 
                                        class="w-32 h-32 rounded-3xl object-cover border-4 border-white shadow-xl bg-white">
                                    
                                    <a href="{{ route('profile.edit') }}" 
                                    class="absolute bottom-2 right-2 bg-white p-2 rounded-xl shadow-md text-pink-600 hover:scale-110 transition md:opacity-0 group-hover:opacity-100">
                                        <i class="fa fa-camera"></i>
                                    </a>
                                </div>

                                <div class="flex-1 text-center md:text-right mb-2">
                                    <h1 class="text-2xl font-black text-gray-800">{{ auth()->user()->name }}</h1>
                                    <p class="text-gray-500 flex items-center justify-center md:justify-start gap-2">
                                        <i class="fa fa-map-marker-alt text-pink-500"></i>
                                        {{ auth()->user()->city ?? 'موقعیت ثبت نشده' }}
                                    </p>
                                </div>

                                <div class="flex gap-3 mb-2">
                                    <a href="{{ route('profile.edit') }}" 
                                    class="px-6 py-2 bg-gray-100 text-gray-700 rounded-2xl font-bold hover:bg-gray-200 transition text-sm">
                                        ویرایش پروفایل
                                    </a>
                                    <button class="p-2 bg-pink-50 text-pink-600 rounded-2xl hover:bg-pink-600 hover:text-white transition">
                                        <i class="fa fa-share-alt"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="flex justify-center md:justify-start gap-8 mt-8 border-t pt-6">
                                <div class="text-center">
                                    <span class="block text-xl font-black text-gray-800">{{ $likesCount ?? 0 }}</span>
                                    <span class="text-xs text-gray-400">لایک‌ها</span>
                                </div>
                                <div class="text-center">
                                    <span class="block text-xl font-black text-gray-800">{{ $totalViews ?? 0 }}</span>
                                    <span class="text-xs text-gray-400">بازدید</span>
                                </div>
                                <div class="text-center">
                                    <span class="block text-xl font-black text-gray-800">{{ $messagesCount ?? 0 }}</span>
                                    <span class="text-xs text-gray-400">پیام‌ها</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                    @include('dashboard.main')
                
            </div>

            <div x-show="openLogoutModal" 
                class="modal-backdrop"
                x-cloak
                x-transition:enter="transition opacity-0 duration-300"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition opacity-100 duration-200"
                x-transition:leave-end="opacity-0">
                
                <div class="absolute inset-0" @click="openLogoutModal = false"></div>

                <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-xs p-6 text-center transform transition-all" @click.stop>
                    <div class="w-12 h-12 bg-red-50 text-red-500 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fa fa-power-off text-xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-800 mb-2">خروج از سایت؟</h3>
                    <p class="text-gray-500 text-sm mb-6">آیا برای خروج مطمئن هستید?</p>
                    
                    <div class="flex gap-2">
                        <button type="button" @click="openLogoutModal = false" 
                                class="flex-1 py-2 bg-gray-100 text-gray-700 rounded-xl font-medium hover:bg-gray-200">
                            انصراف
                        </button>
                        <form action="{{ route('logout') }}" method="POST" class="flex-1">
                            @csrf
                            <button type="submit" class="w-full py-2 bg-red-500 text-white rounded-xl font-bold hover:bg-red-600 shadow-md">
                                خروج
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div x-show="sidebarOpen" 
                x-cloak
                x-transition.opacity
                @click="sidebarOpen = false" 
                class="fixed inset-0 bg-black/50 z-[35] md:hidden backdrop-blur-sm"></div>

        </div>
</main>
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
@endsection