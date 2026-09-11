@extends('layouts.app')
@section('content')
<style>
    .tinder-card{transition:transform .3s ease,box-shadow .3s ease;border-radius:24px;overflow:hidden;position:relative}.tinder-card:active{transform:scale(.97)}.gradient-overlay{background:linear-gradient(to top,rgba(0,0,0,.8) 0%,transparent 60%)}.tab-active{color:#ff5e62;border-bottom:3px solid #ff5e62}
</style>
<div class="min-h-screen bg-gray-50 pb-8" x-data="{ tab: '{{ request()->has('sent_page') ? 'sent' : 'received' }}' }">
    <div class="sticky top-14 z-30 bg-white/90 backdrop-blur-md border-b border-gray-100"><div class="flex justify-around items-center h-14">
        <button type="button" @click="tab='received'" :class="tab==='received'?'tab-active':'text-gray-400'" class="flex-1 h-full font-bold text-sm">کسانی که من را پسندیده‌اند <span class="bg-red-100 text-red-500 px-2 py-0.5 rounded-full text-[10px]">{{ $likedByUsers->total() }}</span></button>
        <button type="button" @click="tab='sent'" :class="tab==='sent'?'tab-active':'text-gray-400'" class="flex-1 h-full font-bold text-sm">پسندیده‌های من <span class="bg-gray-100 px-2 py-0.5 rounded-full text-[10px]">{{ $likedUsers->total() }}</span></button>
    </div></div>

    <section x-show="tab==='received'" x-transition>
        <div class="p-4 grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
        @forelse($likedByUsers as $user)
            <article class="tinder-card h-64 shadow-md bg-white"><a href="{{ route('profile.show', $user->id) }}" class="block h-full"><img src="{{ $user->profilePhotoUrl() }}" alt="{{ $user->name }}" class="w-full h-full object-cover"><div class="absolute inset-0 gradient-overlay pointer-events-none"></div><div class="absolute bottom-3 right-3 left-3 text-white"><p class="font-bold text-sm truncate">{{ $user->name }}@if($user->age !== null)، {{ $user->age }}@endif</p></div></a><form action="{{ route('like.store', $user->id) }}" method="POST" class="absolute bottom-2 left-2 right-2">@csrf<button class="w-full bg-pink-500 text-white py-1.5 rounded-lg text-xs font-bold shadow-lg" type="submit">لایک متقابل</button></form></article>
        @empty
            <div class="col-span-full text-center py-20"><div class="text-6xl mb-4">💔</div><p class="text-gray-500">هنوز کسی شما را لایک نکرده است.</p></div>
        @endforelse
        </div><div class="px-4">{{ $likedByUsers->links() }}</div>
    </section>

    <section x-show="tab==='sent'" x-transition>
        <div class="p-4 grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
        @forelse($likedUsers as $user)
            <a href="{{ route('profile.show', $user->id) }}" class="tinder-card h-64 shadow-md opacity-90"><img src="{{ $user->profilePhotoUrl() }}" alt="{{ $user->name }}" class="w-full h-full object-cover grayscale-[30%]"><div class="absolute inset-0 bg-black/30"></div><div class="absolute bottom-3 right-3 left-3 text-white"><p class="font-bold text-sm truncate">{{ $user->name }}</p><span class="text-[10px] bg-white/20 px-2 py-1 rounded-full">منتظر پاسخ...</span></div></a>
        @empty
            <div class="col-span-full text-center py-20"><p class="text-gray-500">شما هنوز کسی را لایک نکرده‌اید.</p><a href="{{ route('search') }}" class="text-pink-500 font-bold mt-2 block underline">برو به جستجو</a></div>
        @endforelse
        </div><div class="px-4">{{ $likedUsers->links() }}</div>
    </section>
</div>
@endsection
