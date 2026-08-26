@extends('layouts.app')

@section('content')
<style>
    :root {
        --nav-top-height: 3.5rem;
        --nav-bottom-height: 3.5rem;
    }

    main.flex-grow {
        padding: 0 !important;
        margin: 0 !important;
        display: block !important;
    }

    .chat-wrapper {
        display: flex;
        flex-direction: column;
        background-color: #f9fafb;
        position: fixed;
        left: 0;
        right: 0;
        margin-left: auto;
        margin-right: auto;
        max-width: 28rem;
        top: var(--nav-top-height);
        height: calc(
            100dvh -
            (
                var(--nav-top-height) +
                var(--nav-bottom-height)
            )
        );
        z-index: 40;
    }

    #messagesContainer {
        flex: 1;
        overflow-y: auto;
        padding: 1rem;
    }

    .chat-footer {
        flex-shrink: 0;
        background: white;
        border-top: 1px solid #e5e7eb;
        padding: 0.75rem;
        padding-bottom: max(
            0.75rem,
            env(safe-area-inset-bottom)
        );
    }

    .bubble {
        max-width: 85%;
        padding: 0.6rem 1rem;
        border-radius: 1.25rem;
        font-size: 0.875rem;
        line-height: 1.5;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    }

    .bubble-own {
        background: linear-gradient(
            to right,
            #ec4899,
            #f43f5e
        );
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
    <div
        class="flex flex-col bg-gray-50 mx-auto w-full max-w-md shadow-2xl relative"
        style="height: calc(100dvh - 3.5rem);"
    >
        <div
            class="flex items-center justify-between bg-white border-b px-4 py-3 shrink-0 z-10 shadow-sm"
        >
            <div class="flex items-center">
                <a
                    href="{{ url()->previous() }}"
                    class="p-2 -ml-2 hover:bg-gray-100 rounded-full transition"
                >
                    <i
                        class="fas fa-chevron-right text-gray-600"
                    ></i>
                </a>

                <div class="relative ml-3">
                    <img
                        src="{{ $user->profilePhotoUrl() }}"
                        alt="{{ $user->name }}"
                        class="w-10 h-10 rounded-full object-cover border-2 border-pink-50"
                    >

                    <span
                        class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-white rounded-full"
                    ></span>
                </div>

                <div class="mr-3">
                    <div
                        class="font-bold text-gray-800 text-sm"
                    >
                        {{ $user->name }}
                    </div>

                    <div
                        class="text-[10px] text-green-500 font-medium"
                    >
                        آنلاین
                    </div>
                </div>
            </div>

            <button
                type="button"
                class="text-gray-400 p-2"
                aria-label="گزینه‌های گفتگو"
            >
                <i class="fas fa-ellipsis-v"></i>
            </button>
        </div>

        <div
            id="messagesContainer"
            class="flex-1 overflow-y-auto p-4 space-y-4 custom-scrollbar bg-[#fdf2f4]/30"
        >
            @foreach($messages as $message)
                @php
                    $isOwn =
                        $message->sender_id === auth()->id();
                @endphp

                <div
                    class="flex {{ $isOwn ? 'justify-end' : 'justify-start' }} items-end gap-2"
                >
                    @if (
                        $message->status === 'private' &&
                        ! $isOwn
                    )
                        <div
                            class="bg-white border-2 border-dashed border-pink-200 p-4 rounded-2xl rounded-bl-none max-w-[85%] shadow-sm"
                        >
                            <div
                                class="flex items-center text-pink-600 mb-2"
                            >
                                <i class="fas fa-lock-alt ml-2"></i>

                                <span
                                    class="font-bold text-[11px] uppercase tracking-wider text-right"
                                >
                                    محتوای ویژه
                                </span>
                            </div>

                            <p
                                class="text-gray-500 text-xs leading-relaxed text-right"
                            >
                                برای مشاهده این پیام، باید اکانت
                                <strong>پریمیوم</strong>
                                تهیه کنید.
                            </p>

                            <a
                                href="{{ route('premium.upgrade') }}"
                                class="inline-block mt-3 text-[11px] font-bold text-pink-600 hover:underline"
                            >
                                ارتقا حساب کاربری ←
                            </a>
                        </div>
                    @else
                        <div class="max-w-[80%] relative group">
                            <div
                                class="px-4 py-2.5 shadow-sm text-sm {{ $isOwn ? 'bg-gradient-to-r from-pink-500 to-rose-500 text-white rounded-2xl rounded-br-none' : 'bg-white text-gray-800 rounded-2xl rounded-bl-none border border-pink-50' }}"
                            >
                                {{ $message->message }}
                            </div>

                            <span
                                class="text-[9px] mt-1 block text-gray-400 {{ $isOwn ? 'text-left' : 'text-right' }}"
                            >
                                {{ $message->created_at->diffForHumans() }}
                            </span>
                        </div>
                    @endif
                </div>
            @endforeach

            <div id="end-of-messages"></div>
        </div>

        <div class="chat-footer">
            <div
                class="bg-white border-t p-3 pb-safe shrink-0"
            >
                <form
                    id="sendMessageForm"
                    class="flex items-center gap-2"
                >
                    @csrf

                    <input
                        type="hidden"
                        name="receiver_id"
                        value="{{ $user->id }}"
                    >

                    <div
                        class="flex-1 bg-gray-100 rounded-full px-4 py-1 flex items-center border border-transparent focus-within:border-pink-200 focus-within:bg-white transition-all"
                    >
                        <textarea
                            name="message"
                            rows="1"
                            class="flex-1 bg-transparent border-none focus:ring-0 text-sm py-2 resize-none max-h-32"
                            placeholder="چیزی بنویسید..."
                            required
                        ></textarea>

                        <button
                            type="button"
                            class="text-gray-400 hover:text-pink-500 px-2 transition"
                            aria-label="شکلک"
                        >
                            <i class="far fa-smile text-lg"></i>
                        </button>
                    </div>

                    <button
                        type="submit"
                        class="bg-pink-600 hover:bg-pink-700 text-white w-10 h-10 flex items-center justify-center rounded-full shadow-lg shadow-pink-200 transition-transform active:scale-90"
                        aria-label="ارسال پیام"
                    >
                        <i
                            class="fas fa-paper-plane text-sm -mr-0.5"
                        ></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<div
    id="paymentModal"
    class="fixed inset-0 z-[100] hidden items-center justify-center px-6"
>
    <div
        class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm"
    ></div>

    <div
        class="bg-white rounded-[2rem] p-8 w-full max-w-sm text-center relative z-10 shadow-2xl"
    >
        <div
            class="w-20 h-20 bg-pink-50 text-pink-500 rounded-full flex items-center justify-center mx-auto mb-6"
        >
            <i class="fas fa-gem text-3xl"></i>
        </div>

        <h3 class="text-xl font-black text-gray-800 mb-3">
            ارتقا به پریمیوم
        </h3>

        <p
            class="text-gray-500 text-sm mb-8 leading-relaxed"
        >
            برای تجربه گفتگوهای نامحدود و مشاهده پیام‌های ویژه،
            حساب خود را شارژ کنید.
        </p>

        <button
            id="startPaymentButton"
            type="button"
            class="w-full bg-gradient-to-r from-pink-500 to-rose-600 text-white font-bold py-4 rounded-2xl shadow-lg shadow-pink-200 hover:opacity-90 transition"
        >
            ارتقا آنی حساب
        </button>

        <button
            id="closePaymentButton"
            type="button"
            class="w-full mt-4 text-gray-400 text-sm font-medium"
        >
            بعداً انجام می‌دهم
        </button>
    </div>
</div>

<style>
    .pb-safe {
        padding-bottom: max(
            0.75rem,
            env(safe-area-inset-bottom)
        );
    }

    .custom-scrollbar::-webkit-scrollbar {
        width: 4px;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #fecdd3;
        border-radius: 10px;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    #messagesContainer > div {
        animation: fadeInUp 0.4s ease forwards;
    }

    main.pb-safe {
        padding-bottom: 0 !important;
    }
</style>

<script nonce="{{ \Illuminate\Support\Facades\Vite::cspNonce() }}">
    let currentReceiver = null;

    document.addEventListener('DOMContentLoaded', () => {
        const form =
            document.getElementById('sendMessageForm');

        const container =
            document.getElementById('messagesContainer');

        const startPaymentButton =
            document.getElementById('startPaymentButton');

        const closePaymentButton =
            document.getElementById('closePaymentButton');

        startPaymentButton?.addEventListener(
            'click',
            startPayment
        );

        closePaymentButton?.addEventListener(
            'click',
            closePaymentModal
        );

        if (container) {
            container.scrollTop = container.scrollHeight;
        }

        form?.addEventListener('submit', async (event) => {
            event.preventDefault();

            const formData = new FormData(form);

            const messageText = form
                .querySelector('textarea[name="message"]')
                .value;

            try {
                const response = await fetch(
                    "{{ route('messages.store') }}",
                    {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document
                                .querySelector(
                                    'input[name="_token"]'
                                )
                                .value,
                            'Accept': 'application/json',
                        },
                        body: formData,
                    }
                );

                const data = await response.json();

                if (response.status === 422) {
                    if (data.errors) {
                        window.alert(
                            Object.values(data.errors)[0][0]
                        );
                    }

                    return;
                }

                if (
                    data.error === 'PAYMENT_REQUIRED' ||
                    data.error === 'PREMIUM_REQUIRED'
                ) {
                    currentReceiver =
                        data.receiver_id ?? null;

                    openPaymentModal();
                    return;
                }

                if (!response.ok) {
                    throw new Error('Server Error');
                }

                if (container) {
                    const messageRow =
                        document.createElement('div');

                    messageRow.className =
                        'flex justify-end items-end gap-2';

                    const messageWrapper =
                        document.createElement('div');

                    messageWrapper.className =
                        'max-w-[80%] relative';

                    const messageBubble =
                        document.createElement('div');

                    messageBubble.className =
                        'px-4 py-2.5 shadow-sm text-sm ' +
                        'bg-gradient-to-r from-pink-500 ' +
                        'to-rose-500 text-white rounded-2xl ' +
                        'rounded-br-none';

                    /*
                     * متن پیام فقط به‌عنوان متن قرار می‌گیرد.
                     * HTML ورودی کاربر اجرا نخواهد شد.
                     */
                    messageBubble.textContent = messageText;

                    messageWrapper.appendChild(
                        messageBubble
                    );

                    messageRow.appendChild(
                        messageWrapper
                    );

                    const endMarker =
                        document.getElementById(
                            'end-of-messages'
                        );

                    if (endMarker) {
                        container.insertBefore(
                            messageRow,
                            endMarker
                        );
                    } else {
                        container.appendChild(messageRow);
                    }

                    container.scrollTo({
                        top: container.scrollHeight,
                        behavior: 'smooth',
                    });
                }

                form.reset();
            } catch (error) {
                console.error(error);
            }
        });
    });

    function openPaymentModal() {
        const modal =
            document.getElementById('paymentModal');

        modal?.classList.remove('hidden');
        modal?.classList.add('flex');
    }

    function closePaymentModal() {
        const modal =
            document.getElementById('paymentModal');

        modal?.classList.add('hidden');
        modal?.classList.remove('flex');
    }

    function startPayment() {
        window.location.href =
            "{{ route('premium.upgrade') }}";
    }
</script>
@endsection
