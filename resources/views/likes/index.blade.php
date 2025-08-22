{{-- resources/views/likes/index.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="max-w-6xl mx-auto px-4 py-8">

        {{-- بخش لایک‌های من --}}
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-4 border-b pb-2">
                ❤️ افرادی که لایک کرده‌ام
            </h2>
            @if ($likedUsers->isEmpty())
                <p class="text-gray-500">شما هنوز کسی را لایک نکرده‌اید.</p>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach ($likedUsers as $u)
                        <div class="flex items-center bg-gray-50 p-4 rounded-lg shadow hover:shadow-lg transition">
                            <img src="{{ $u->profile_picture ? asset('storage/' . $u->profile_picture) : asset('images/default-avatar.png') }}"
                                alt="{{ $u->name }}" class="w-14 h-14 rounded-full object-cover border border-gray-200">
                            <div class="ml-4">
                                <p class="text-lg font-semibold text-gray-700">{{ $u->name }}</p>
                                <p class="text-sm text-gray-500">{{ $u->city ?? 'بدون شهر' }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- بخش کسانی که من را لایک کرده‌اند --}}
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-4 border-b pb-2">
                🌟 افرادی که من را لایک کرده‌اند
            </h2>
            @if ($likedByUsers->isEmpty())
                <p class="text-gray-500">هنوز کسی شما را لایک نکرده است.</p>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach ($likedByUsers as $u)
                        <div class="flex items-center bg-gray-50 p-4 rounded-lg shadow hover:shadow-lg transition">
                            <img src="{{ $u->profile_picture ? asset('storage/' . $u->profile_picture) : asset('images/default-avatar.png') }}"
                                alt="{{ $u->name }}"
                                class="w-14 h-14 rounded-full object-cover border border-gray-200">
                            <div class="ml-4">
                                <p class="text-lg font-semibold text-gray-700">{{ $u->name }}</p>
                                <p class="text-sm text-gray-500">{{ $u->city ?? 'بدون شهر' }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>
@endsection
