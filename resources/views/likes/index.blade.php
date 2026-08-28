@extends('layouts.app')

@section('content')
<style>
    /* استایل اختصاصی برای موبایل */
    .tinder-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border-radius: 24px;
        overflow: hidden;
        position: relative;
    }
    .tinder-card:active {
        transform: scale(0.95);
    }
    .gradient-overlay {
        background: linear-gradient(to top, rgba(0,0,0,0.8) 0%, transparent 60%);
    }
    .tab-active {
        color: #ff5e62;
        border-bottom: 3px solid #ff5e62;
    }
    /* اسکرول نرم برای لیست‌های موبایل */
    .hide-scrollbar::-webkit-scrollbar { display: none; }
    .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>

<div class="min-h-screen bg-gray-50 pb-20" x-data="{ tab: 'received' }">
    
    <div class="sticky top-0 z-30 bg-white/80 backdrop-blur-md border-b border-gray-100">
        <div class="flex justify-around items-center h-14">
            <button @click="tab = 'received'" 
                    :class="tab === 'received' ? 'tab-active' : 'text-gray-400'"
                    class="flex-1 h-full font-bold text-sm transition-all">
                لایک‌های من <span class="bg-red-100 text-red-500 px-2 py-0.5 rounded-full text-[10px]">{{ count($likedByUsers) }}</span>
            </button>
            <button @click="tab = 'sent'" 
                    :class="tab === 'sent' ? 'tab-active' : 'text-gray-400'"
                    class="flex-1 h-full font-bold text-sm transition-all">
                پسندیده‌های من
            </button>
        </div>
    </div>

    <div x-show="tab === 'received'" x-transition class="p-4 grid grid-cols-2 gap-4">
        @forelse($likedByUsers as $user)
            <div class="tinder-card h-64 shadow-md bg-white">
                <img src="{{ $user->profilePhotoUrl() }}" class="w-full h-full object-cover">
                <div class="absolute inset-0 gradient-overlay"></div>
                <div class="absolute bottom-3 right-3 left-3 text-white">
                    <p class="font-bold text-sm truncate">{{ $user->name }}، {{ $user->age ?? '۲۵' }}</p>
                    <div class="flex gap-2 mt-2">
                        <form action="{{ route('like.store', $user->id) }}" method="POST" class="flex-1">
                            @csrf
                            <button class="w-full bg-pink-500 py-1.5 rounded-lg text-[10px] font-bold shadow-lg">لایک متقابل</button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-2 text-center py-20">
                <div class="text-6xl mb-4">💔</div>
                <p class="text-gray-500">هنوز کسی شما را لایک نکرده است.</p>
            </div>
        @endforelse
    </div>

    <div x-show="tab === 'sent'" x-transition class="p-4 grid grid-cols-2 gap-4">
        @forelse($likedUsers as $user)
            <div class="tinder-card h-64 shadow-md opacity-90">
                <img src="{{ $user->profilePhotoUrl() }}" class="w-full h-full object-cover grayscale-[30%]">
                <div class="absolute inset-0 bg-black/20"></div>
                <div class="absolute bottom-3 right-3 left-3 text-white">
                    <p class="font-bold text-sm truncate">{{ $user->name }}</p>
                    <span class="text-[10px] bg-white/20 px-2 py-1 rounded-full backdrop-blur-sm">منتظر پاسخ...</span>
                </div>
            </div>
        @empty
            <div class="col-span-2 text-center py-20">
                <p class="text-gray-500">شما هنوز کسی را لایک نکرده‌اید.</p>
                <a href="{{ route('dashboard') }}" class="text-pink-500 font-bold mt-2 block italic underline">برو به جستجو</a>
            </div>
        @endforelse
    </div>

    <div class="fixed bottom-6 left-1/2 -translate-x-1/2 bg-white px-6 py-3 rounded-full shadow-2xl border border-gray-100 flex gap-8 z-40">
        <a href="{{ route('dashboard') }}" class="text-gray-400 hover:text-pink-500"><i class="fa fa-home text-xl"></i></a>
        <a href="#" class="text-pink-500 scale-125"><i class="fa fa-heart text-xl"></i></a>
        <a href="{{ route('messages.index') }}" class="text-gray-400 hover:text-pink-500"><i class="fa fa-comment text-xl"></i></a>
    </div>
</div>
@endsection
