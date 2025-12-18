@extends('layouts.app')

@section('content')

<style>
    /* ۱. تعریف متغیرهای ارتفاع بر اساس کلاس‌های Tailwind تمپلیت شما */
    :root {
        --nav-top-height: 3.5rem;    /* معادل h-14 نوار بالا */
        --nav-bottom-height: 3.5rem; /* معادل h-14 نوار پایین */
    }

    /* ۲. اصلاح رفتار Main برای حذف پدینگ‌های مزاحم */
    main.flex-grow {
        padding: 0 !important;
        margin: 0 !important;
        display: block !important;
    }

    /* ۳. کانتینر چت با محاسبه داینامیک */
    .chat-wrapper {
        display: flex;
        flex-direction: column;
        background-color: #f9fafb;
        position: fixed;
        left: 0;
        right: 0;
        margin-left: auto;
        margin-right: auto;
        max-width: 28rem; /* max-w-md */
        
        /* شروع از زیر نوار بالا */
        top: var(--nav-top-height);
        
        /* ارتفاع = کل صفحه منهای مجموع نوار بالا و پایین و حاشیه امن آیفون */
        height: calc(100dvh - (var(--nav-top-height) + var(--nav-bottom-height)));
        
        z-index: 40; /* برای اینکه زیر نوار پایین (z-50) بماند */
    }

    /* ۴. بخش پیام‌ها با اسکرول خودکار */
    #messagesContainer {
        flex: 1;
        overflow-y: auto;
        padding: 1rem;
    }

    /* ۵. فوتر چت که دقیقاً مماس با نوار پایین سایت قرار می‌گیرد */
    .chat-footer {
        flex-shrink: 0;
        background: white;
        border-top: 1px solid #e5e7eb;
        padding: 0.75rem;
        /* حاشیه امن برای گوشی‌های جدید */
        padding-bottom: max(0.75rem, env(safe-area-inset-bottom));
    }

    /* استایل‌های ظاهری مدرن (تیندری) */
    .bubble {
        max-width: 85%;
        padding: 0.6rem 1rem;
        border-radius: 1.25rem;
        font-size: 0.875rem;
        line-height: 1.5;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    }
    .bubble-own {
        background: linear-gradient(to right, #ec4899, #f43f5e);
        color: white;
        border-bottom-left-radius: 0.25rem;
    }
    .bubble-other {
        background: white;
        color: #1f2937;
        border: 1px solid #f3f4f6;
        border-bottom-right-radius: 0.25rem;
    }
</style>

<div class="chat-wrapper">
{{-- کانتینر اصلی چت - اصلاح شده برای نمایش کامل --}}
    <div class="flex flex-col bg-gray-50 mx-auto w-full max-w-md shadow-2xl relative" style="height: calc(100dvh - 3.5rem);">
    
    {{-- هدر چت مدرن --}}
    <div class="flex items-center justify-between bg-white border-b px-4 py-3 shrink-0 z-10 shadow-sm">
        <div class="flex items-center">
            <a href="{{ url()->previous() }}" class="p-2 -ml-2 hover:bg-gray-100 rounded-full transition">
                <i class="fas fa-chevron-right text-gray-600"></i>
            </a>

            <div class="relative ml-3">
                <img src="{{ $user->profile_picture ? asset('storage/'.$user->profile_picture) : 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=ff4b6b&color=fff' }}"
                     class="w-10 h-10 rounded-full object-cover border-2 border-pink-50">
                <span class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-white rounded-full"></span>
            </div>

            <div class="mr-3">
                <div class="font-bold text-gray-800 text-sm">{{ $user->name }}</div>
                <div class="text-[10px] text-green-500 font-medium">آنلاین</div>
            </div>
        </div>
        <button class="text-gray-400 p-2"><i class="fas fa-ellipsis-v"></i></button>
    </div>

    {{-- کانتینر پیام‌ها با اسکرول اصلاح شده --}}
    <div id="messagesContainer" class="flex-1 overflow-y-auto p-4 space-y-4 custom-scrollbar bg-[#fdf2f4]/30">
        @foreach($messages as $message)
            @php $isOwn = $message->sender_id === auth()->id(); @endphp

            <div class="flex {{ $isOwn ? 'justify-end' : 'justify-start' }} items-end gap-2">
                @if ($message->status === 'private' && ! $isOwn)
                    {{-- حالت قفل پریمیوم --}}
                    <div class="bg-white border-2 border-dashed border-pink-200 p-4 rounded-2xl rounded-bl-none max-w-[85%] shadow-sm">
                        <div class="flex items-center text-pink-600 mb-2">
                            <i class="fas fa-lock-alt ml-2"></i>
                            <span class="font-bold text-[11px] uppercase tracking-wider text-right">محتوای ویژه</span>
                        </div>
                        <p class="text-gray-500 text-xs leading-relaxed text-right">
                            برای مشاهده این پیام، باید اکانت <strong>پریمیوم</strong> تهیه کنید.
                        </p>
                        <a href="{{ route('premium.upgrade') }}" class="inline-block mt-3 text-[11px] font-bold text-pink-600 hover:underline">
                             ارتقا حساب کاربری ←
                        </a>
                    </div>
                @else
                    {{-- پیام واقعی --}}
                    <div class="max-w-[80%] relative group">
                        <div class="px-4 py-2.5 shadow-sm text-sm {{ $isOwn ? 'bg-gradient-to-r from-pink-500 to-rose-500 text-white rounded-2xl rounded-br-none' : 'bg-white text-gray-800 rounded-2xl rounded-bl-none border border-pink-50' }}">
                            {{ $message->message }}
                        </div>
                        <span class="text-[9px] mt-1 block text-gray-400 {{ $isOwn ? 'text-left' : 'text-right' }}">
                            {{ $message->created_at->diffForHumans() }}
                        </span>
                    </div>
                @endif
            </div>
        @endforeach
        <div id="end-of-messages"></div>
    </div>

    {{-- بخش ارسال پیام - ثابت در پایین کانتینر چت --}}
    <div class="chat-footer">

     <div class="bg-white border-t p-3 pb-safe shrink-0">
        <form id="sendMessageForm" class="flex items-center gap-2">
            @csrf
            <input type="hidden" name="receiver_id" value="{{ $user->id }}">

            <div class="flex-1 bg-gray-100 rounded-full px-4 py-1 flex items-center border border-transparent focus-within:border-pink-200 focus-within:bg-white transition-all">
                <textarea name="message"
                          rows="1"
                          class="flex-1 bg-transparent border-none focus:ring-0 text-sm py-2 resize-none max-h-32"
                          placeholder="چیزی بنویسید..."
                          required></textarea>
                <button type="button" class="text-gray-400 hover:text-pink-500 px-2 transition">
                    <i class="far fa-smile text-lg"></i>
                </button>
            </div>

            <button type="submit"
                    class="bg-pink-600 hover:bg-pink-700 text-white w-10 h-10 flex items-center justify-center rounded-full shadow-lg shadow-pink-200 transition-transform active:scale-90">
                <i class="fas fa-paper-plane text-sm -mr-0.5"></i>
            </button>
        </form>
     </div>
    </div>
    </div>
</div >
{{-- مدال پرداخت مدرن --}}
<div id="paymentModal" class="fixed inset-0 z-[100] hidden items-center justify-center px-6">
    <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm"></div>
    <div class="bg-white rounded-[2rem] p-8 w-full max-w-sm text-center relative z-10 shadow-2xl">
        <div class="w-20 h-20 bg-pink-50 text-pink-500 rounded-full flex items-center justify-center mx-auto mb-6">
            <i class="fas fa-gem text-3xl"></i>
        </div>
        <h3 class="text-xl font-black text-gray-800 mb-3">ارتقا به پریمیوم</h3>
        <p class="text-gray-500 text-sm mb-8 leading-relaxed">برای تجربه گفتگوهای نامحدود و مشاهده پیام‌های ویژه، حساب خود را شارژ کنید.</p>

        <button onclick="startPayment()" class="w-full bg-gradient-to-r from-pink-500 to-rose-600 text-white font-bold py-4 rounded-2xl shadow-lg shadow-pink-200 hover:opacity-90 transition">
            ارتقا آنی حساب
        </button>

        <button onclick="closePaymentModal()" class="w-full mt-4 text-gray-400 text-sm font-medium">
            بعداً انجام می‌دهم
        </button>
    </div>
</div>

<style>
    .pb-safe { padding-bottom: max(0.75rem, env(safe-area-inset-bottom)); }
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #fecdd3; border-radius: 10px; }
    
    /* انیمیشن پیام‌ها */
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    #messagesContainer > div { animation: fadeInUp 0.4s ease forwards; }

    /* اصلاح برای موبایل که زیر نوار پایین نرود */
    main.pb-safe { padding-bottom: 0 !important; }
</style>

{{-- اسکریپت‌های دست‌نخورده شما --}}
<script>
let currentReceiver = null;
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('sendMessageForm');
    const container = document.getElementById('messagesContainer');
    if (container) container.scrollTop = container.scrollHeight;

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(form);
        const messageText = form.querySelector('textarea[name="message"]').value;

        fetch("{{ route('messages.store') }}", {
            method: "POST",
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name=_token]').value,
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(async res => {
            const data = await res.json();
            if (res.status === 402) return data;
            if (res.status === 422) {
                if (data.errors) alert(Object.values(data.errors)[0][0]);
                throw new Error('VALIDATION_FAILED'); 
            }
            if (!res.ok) throw new Error('Server Error');
            return data;
        })
        .then(data => {
            if (data.error === 'PAYMENT_REQUIRED' || data.error === 'PREMIUM_REQUIRED') {
                currentReceiver = data.receiver_id ?? null;
                openPaymentModal();
                return;
            }
            if (container) {
                const newMessageHtml = `
                    <div class="flex justify-end items-end gap-2">
                        <div class="max-w-[80%] relative">
                            <div class="px-4 py-2.5 shadow-sm text-sm bg-gradient-to-r from-pink-500 to-rose-500 text-white rounded-2xl rounded-br-none">
                                ${messageText}
                            </div>
                        </div>
                    </div>`;
                container.insertAdjacentHTML('beforeend', newMessageHtml);
                container.scrollTo({ top: container.scrollHeight, behavior: 'smooth' });
            }
            form.reset();
        }).catch(err => console.error(err));
    });
});

function openPaymentModal() {
    const m = document.getElementById('paymentModal');
    m.classList.remove('hidden'); m.classList.add('flex');
}
function closePaymentModal() {
    const m = document.getElementById('paymentModal');
    m.classList.add('hidden'); m.classList.remove('flex');
}
function startPayment() {
    if (!currentReceiver) return alert('خطا در شناسایی');
    fetch("{{ route('payments.create') }}", {
        method: "POST",
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify({ receiver_id: currentReceiver })
    })
    .then(res => res.json())
    .then(data => data.payment_url ? window.location.href = data.payment_url : alert('خطا'))
    .catch(err => console.error(err));
}
</script>

@endsection