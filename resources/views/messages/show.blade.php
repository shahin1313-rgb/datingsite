@extends('layouts.app')

{{-- لودینگ اسپینر --}}
<div id="loadingSpinner" role="status"
     class="fixed inset-0 bg-white flex items-center justify-center z-50 transition-opacity duration-300">
    <div class="text-center">
        <svg class="animate-spin h-12 w-12 text-blue-600 mx-auto mb-4" xmlns="http://www.w3.org/2000/svg" fill="none"
             viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor"
                  d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <span class="text-gray-600 text-lg">در حال بارگذاری...</span>
    </div>
</div>

<script>
    window.addEventListener('load', () => {
        const spinner = document.getElementById('loadingSpinner');
        spinner.style.opacity = '0';
        setTimeout(() => spinner.remove(), 400);
    });
</script>

@section('content')
    {{-- تمام صفحه چت دقیقاً زیر navbar سایت شروع می‌شود (حتی اگر navbar fixed باشد) --}}
    <div class="w-full max-w-5xl mx-auto px-2 pt-16"> {{-- اگر navbar شما h-16 است → pt-16، اگر h-20 است → pt-20 --}}

        {{-- کانتینر چت که تمام فضای باقی‌مانده را پر می‌کند --}}
        <div class="h-[calc(100vh-4rem)] flex flex-col bg-white shadow-2xl rounded-lg overflow-hidden">

            {{-- هدر چت → حالا ۱۰۰٪ زیر navbar سایت است و هیچ تداخلی ندارد --}}
            <div class="flex items-center justify-between bg-blue-600 text-white px-4 py-3 shrink-0 border-b border-blue-700">
                <div class="flex items-center flex-1">
                    <a href="{{ url()->previous() }}" class="text-white hover:bg-white/20 rounded-full p-2 transition mr-2">
                        ←
                    </a>

                    <img src="{{ $user->profile_picture 
                        ? asset('storage/' . $user->profile_picture) 
                        : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&size=96&background=e5e7eb&color=374151' }}"
                         class="w-12 h-12 rounded-full object-cover mr-4 border-3 border-white shadow-lg"
                         alt="{{ $user->name }}">

                    <div>
                        <h4 class="text-lg font-semibold">{{ $user->name }}</h4>

                        @php
                            $lastLogin = \Carbon\Carbon::make($user->last_login_at);
                            $isOnline = $lastLogin && $lastLogin->diffInMinutes(now()) < 5;
                        @endphp

                        <p class="text-sm opacity-90 flex items-center gap-1.5">
                            @if($isOnline)
                                <span class="w-2.5 h-2.5 bg-green-400 rounded-full animate-pulse"></span>
                                <span class="mr-1">آنلاین</span>
                            @else
                                آخرین بازدید: 
                                <span dir="ltr">{{ $lastLogin?->diffForHumans() ?? 'نامشخص' }}</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            {{-- لیست پیام‌ها --}}
            <div id="messagesContainer" class="flex-1 overflow-y-auto p-4 bg-gray-50/90">
                @php $previousSenderId = null; @endphp

                @forelse ($messages as $message)
                    @php
                        $isOwn = $message->sender_id == auth()->id();
                        $isNewGroup = is_null($previousSenderId) || $previousSenderId !== $message->sender_id;
                        $previousSenderId = $message->sender_id;
                    @endphp

                    <div class="flex {{ $isOwn ? 'justify-end' : 'justify-start' }} w-full {{ $isNewGroup ? 'mt-8' : 'mt-2' }}">
                        <div class="flex items-end gap-3 {{ $isOwn ? 'flex-row-reverse' : '' }} max-w-[85%]">
                            @if($isNewGroup)
                                <img src="{{ $message->sender->profile_picture 
                                    ? asset('storage/' . $message->sender->profile_picture) 
                                    : 'https://ui-avatars.com/api/?name=' . urlencode($message->sender->name) . '&size=80&background=e5e7eb&color=374151' }}"
                                     class="w-10 h-10 rounded-full object-cover flex-shrink-0"
                                     alt="{{ $message->sender->name }}">
                            @else
                                <div class="w-10"></div>
                            @endif

                            <div class="max-w-xs md:max-w-md p-3.5 rounded-2xl shadow-sm
                                {{ $isOwn 
                                    ? 'bg-blue-500 text-white rounded-br-none' 
                                    : 'bg-white text-gray-800 rounded-bl-none border border-gray-200' }}">

                                <p class="text-sm leading-relaxed break-words">{{ $message->message }}</p>

                                <div class="flex items-center gap-1.5 mt-1.5 text-xs opacity-80 {{ $isOwn ? 'justify-end' : 'justify-start' }}">
                                    <span>{{ $message->created_at->format('H:i') }}</span>

                                    @if($isOwn)
                                        @if($message->read_at)
                                            <svg class="w-4 h-4 text-cyan-300" fill="currentColor" viewBox="0 0 16 16">
                                                <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3-3a.5.5 0 1 1 .708-.708L6.5 10.793l6.646-6.647a.5.5 0 0 1 .708 0z"/>
                                                <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3-3a.5.5 0 1 1 .708-.708L6.5 10.793l6.646-6.647a.5.5 0 0 1 .708 0z"/>
                                            </svg>
                                        @else
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-gray-500 py-12">
                        هنوز پیامی ارسال نشده. اولین پیام را شما بفرستید! 💬
                    </div>
                @endforelse

                <div id="end-of-messages"></div>
            </div>

            {{-- فرم ارسال پیام (داخل کانتینر - بدون fixed و بدون تداخل) --}}
            <div class="bg-white border-t border-gray-200 p-4 shrink-0">
                <form action="{{ route('messages.store', $user) }}" method="POST" class="flex items-end gap-3">
                    @csrf
                    <textarea name="message"
                              rows="1"
                              placeholder="پیام خود را بنویسید..."
                              class="flex-1 resize-none border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                              autocomplete="off" required></textarea>

                    <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white p-3.5 rounded-xl shadow-lg transition hover:scale-110">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const end = document.getElementById('end-of-messages');
            end.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        });
    </script>
@endsection