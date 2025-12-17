@extends('layouts.app')

@section('content')



<div class="fixed inset-0 flex flex-col bg-[#f0f2f5] max-w-md mx-auto shadow-2xl">
    
    {{-- هدر چت مدرن --}}
    <div class="flex items-center justify-between bg-white border-b px-4 py-3 shrink-0 z-10 shadow-sm">
        <div class="flex items-center">
            <a href="{{ url()->previous() }}" class="p-2 -ml-2 hover:bg-gray-100 rounded-full transition">
                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>

            <div class="relative ml-2">
                <img src="{{ $user->profile_picture ? asset('storage/'.$user->profile_picture) : 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=0D8ABC&color=fff' }}"
                     class="w-10 h-10 rounded-full object-cover border border-gray-100">
                <span class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-white rounded-full"></span>
            </div>

            <div class="ml-3">
                <div class="font-bold text-gray-800 text-sm leading-tight">{{ $user->name }}</div>
                <div class="text-[11px] text-green-600 font-medium">آنلاین</div>
            </div>
        </div>
        
        <button class="text-gray-400 hover:text-gray-600">
            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/></svg>
        </button>
    </div>

    {{-- کانتینر پیام‌ها --}}
    <div id="messagesContainer" class="flex-1 overflow-y-auto p-4 space-y-4 custom-scrollbar">
        @foreach($messages as $message)
            @php $isOwn = $message->sender_id === auth()->id(); @endphp

            <div class="flex {{ $isOwn ? 'justify-end' : 'justify-start' }} animate-fade-in-up">
                
                @if ($message->status === 'private' && ! $isOwn)
                    <div class="bg-white border-2 border-dashed border-blue-100 p-4 rounded-2xl rounded-bl-none max-w-[80%] shadow-sm">
                        <div class="flex items-center text-blue-600 mb-2">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/></svg>
                            <span class="font-bold text-xs uppercase tracking-wider">محتوای ویژه</span>
                        </div>
                        <p class="text-gray-500 text-xs leading-relaxed">
                            برای مشاهده این پیام و ادامه گفتگو، اشتراک پریمیوم تهیه کنید.
                        </p>
                        <a href="{{ route('premium.upgrade') }}" class="inline-block mt-3 text-xs font-bold text-blue-600 hover:underline">
                            ارتقا حساب کاربری ←
                        </a>
                    </div>
                @else
                    <div class="relative group max-w-[80%]">
                        <div class="px-4 py-2.5 shadow-sm 
                            {{ $isOwn 
                                ? 'bg-blue-600 text-white rounded-[20px] rounded-br-none' 
                                : 'bg-white text-gray-800 rounded-[20px] rounded-bl-none border border-gray-100' }}">
                            <p class="text-sm leading-6">{{ $message->message }}</p>
                        </div>
                        <span class="text-[9px] mt-1 block text-gray-400 {{ $isOwn ? 'text-right' : 'text-left' }}">
                            {{ verta($message->created_at)->format('H:i') }}
                        </span>
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    {{-- بخش ارسال پیام --}}
    <div class="bg-white p-3 border-t pb-safe">
        <form id="sendMessageForm" class="flex items-end gap-2">
            @csrf
            <input type="hidden" name="receiver_id" value="{{ $user->id }}">

            <div class="flex-1 bg-gray-100 rounded-[24px] px-4 py-1.5 flex items-center">
                <textarea name="message"
                          rows="1"
                          class="flex-1 bg-transparent border-none focus:ring-0 text-sm max-h-32 resize-none py-2"
                          placeholder="پیام شما..."
                          required></textarea>
                <button type="button" class="text-gray-400 hover:text-blue-600 p-1">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </button>
            </div>

            <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white p-3 rounded-full transition-all transform active:scale-95 shadow-md">
                <svg class="w-5 h-5 rotate-90" fill="currentColor" viewBox="0 0 20 20"><path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"/></svg>
            </button>
        </form>
    </div>
</div>

{{-- مدال پرداخت مدرن --}}
<div id="paymentModal" class="fixed inset-0 z-[60] hidden">
    <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm"></div>
    <div class="absolute bottom-0 inset-x-0 p-4 transform transition-transform">
        <div class="bg-white rounded-3xl p-6 text-center shadow-2xl">
            <div class="w-16 h-16 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            </div>
            <h3 class="text-xl font-extrabold text-gray-800 mb-2">ارتقا به پریمیوم</h3>
            <p class="text-gray-500 text-sm mb-6">برای ارسال پیام‌های نامحدود و تجربه بهتر، نیاز به شارژ حساب دارید.</p>
            
            <button onclick="startPayment()" class="w-full bg-blue-600 text-white font-bold py-3 rounded-2xl shadow-lg shadow-blue-200 active:scale-95 transition">
                شروع پرداخت
            </button>
            
            <button onclick="closePaymentModal()" class="w-full mt-3 text-gray-400 font-medium py-2">
                فعلاً نه، ممنون
            </button>
        </div>
    </div>
</div>

<style>
    .animate-fade-in-up {
        animation: fadeInUp 0.3s ease-out forwards;
    }
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
    .pb-safe { padding-bottom: env(safe-area-inset-bottom); }
</style>



@endsection