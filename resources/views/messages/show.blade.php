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
        max-width: 56rem;
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
        class="flex flex-col bg-gray-50 mx-auto w-full max-w-4xl shadow-2xl relative"
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

                    @if ($user->isOnline())
                        <span
                            class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-white rounded-full"
                        ></span>
                    @endif
                </div>

                <div class="mr-3">
                    <div
                        class="font-bold text-gray-800 text-sm"
                    >
                        {{ $user->name }}
                    </div>

                    <div class="text-[10px] {{ $user->isOnline() ? 'text-green-500' : 'text-gray-400' }} font-medium">
                        {{ $user->isOnline() ? 'آنلاین' : 'آفلاین' }}
                    </div>
                </div>
            </div>

            <div class="relative">
                <button
                    id="chatSafetyMenuButton"
                    type="button"
                    class="min-w-11 min-h-11 text-gray-500 hover:text-gray-700 hover:bg-gray-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-pink-500 rounded-full transition"
                    aria-label="گزینه‌های ایمنی گفتگو"
                    aria-controls="chatSafetyMenu"
                    aria-expanded="false"
                >
                    <i class="fas fa-ellipsis-v"></i>
                </button>

                <div
                    id="chatSafetyMenu"
                    class="hidden absolute left-0 mt-2 w-52 bg-white border border-gray-100 rounded-2xl shadow-2xl overflow-hidden z-30"
                    role="menu"
                >
                    <a
                        href="{{ route('profile.show', $user->id) }}"
                        class="min-h-11 px-4 flex items-center gap-3 text-sm text-gray-700 hover:bg-gray-50 focus-visible:outline-none focus-visible:bg-gray-50"
                        role="menuitem"
                    >
                        <i
                            class="fas fa-user text-gray-400"
                            aria-hidden="true"
                        ></i>

                        مشاهده پروفایل
                    </a>

                    <button
                        type="button"
                        data-chat-report-open
                        class="w-full min-h-11 px-4 flex items-center gap-3 text-sm text-amber-700 hover:bg-amber-50 focus-visible:outline-none focus-visible:bg-amber-50"
                        role="menuitem"
                    >
                        <i
                            class="fas fa-flag"
                            aria-hidden="true"
                        ></i>

                        گزارش کاربر
                    </button>

                    <form
                        action="{{ route('user.block', $user->id) }}"
                        method="POST"
                        data-sweet-block
                        data-block-name="{{ $user->name }}"
                        data-confirm="با مسدود کردن این کاربر، ارتباط و لایک‌های قبلی حذف می‌شوند. ادامه می‌دهید؟"
                    >
                        @csrf

                        <button
                            type="submit"
                            data-chat-block
                            class="w-full min-h-11 px-4 flex items-center gap-3 text-sm text-red-600 hover:bg-red-50 focus-visible:outline-none focus-visible:bg-red-50"
                            role="menuitem"
                        >
                            <i
                                class="fas fa-user-slash"
                                aria-hidden="true"
                            ></i>

                            مسدود کردن
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div
            id="messagesContainer"
            class="flex-1 overflow-y-auto p-4 space-y-4 custom-scrollbar bg-[#fdf2f4]/30"
        >
            @if (session('success'))
                <div
                    class="p-3 rounded-xl bg-green-50 border border-green-200 text-green-700 text-sm"
                    role="status"
                >
                    {{ session('success') }}
                </div>
            @endif

            @if ($messages->hasPages())
                <nav
                    class="flex items-center justify-between gap-3 pb-2"
                    aria-label="صفحه‌بندی پیام‌ها"
                >
                    @if ($messages->hasMorePages())
                        <a
                            href="{{ $messages->nextPageUrl() }}"
                            class="text-xs font-bold text-pink-600 hover:text-pink-700"
                        >
                            پیام‌های قدیمی‌تر
                        </a>
                    @else
                        <span class="text-xs text-gray-400">
                            ابتدای گفتگو
                        </span>
                    @endif

                    @if ($messages->currentPage() > 1)
                        <a
                            href="{{ $messages->previousPageUrl() }}"
                            class="text-xs font-bold text-pink-600 hover:text-pink-700"
                        >
                            پیام‌های جدیدتر
                        </a>
                    @endif
                </nav>
            @endif

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
                        ! $isOwn &&
                        ! $canViewPrivateMessages
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
                <div id="messageFeedback" class="hidden mb-2 rounded-xl px-3 py-2 text-sm" role="status" aria-live="polite"></div>
                <form
                    id="sendMessageForm"
                    method="POST"
                    action="{{ route('messages.store') }}"
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
                            id="messageInput"
                            name="message"
                            rows="1"
                            class="flex-1 bg-transparent border-none text-gray-900 caret-pink-600 placeholder:text-gray-400 focus:text-gray-900 focus:ring-0 text-sm py-2 resize-none max-h-32"
                            placeholder="چیزی بنویسید..."
                            maxlength="1000"
                            required
                        ></textarea>

                        <div class="relative">
                        <button
                            type="button"
                            id="emojiButton"
                            class="text-gray-400 hover:text-pink-500 px-2 transition"
                            aria-label="شکلک"
                        >
                            <i class="far fa-smile text-lg"></i>
                        </button>
                        <div id="emojiPicker" class="hidden absolute bottom-10 left-0 z-20 w-48 rounded-2xl bg-white border border-gray-200 shadow-xl p-2 grid grid-cols-6 gap-1">
                            @foreach(['😀','😂','😍','🥰','😘','😊','😉','🤗','😎','❤️','👍','🎉'] as $emoji)<button type="button" data-emoji="{{ $emoji }}" class="p-1 rounded hover:bg-pink-50" aria-label="{{ $emoji }}">{{ $emoji }}</button>@endforeach
                        </div>
                        </div>
                    </div>

                    <button
                        type="submit"
                        id="sendMessageButton"
                        class="bg-pink-600 hover:bg-pink-700 text-white w-10 h-10 flex items-center justify-center rounded-full shadow-lg shadow-pink-200 transition-transform active:scale-90"
                        aria-label="ارسال پیام"
                    >
                        <svg
                            data-send-icon
                            class="h-5 w-5 -rotate-12"
                            viewBox="0 0 24 24"
                            fill="none"
                            xmlns="http://www.w3.org/2000/svg"
                            aria-hidden="true"
                        >
                            <path
                                d="M21.5 3.5 9.8 15.2M21.5 3.5l-7.45 17-4.25-5.3-6.3-2.25 18-9.45Z"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            />
                        </svg>

                        <svg
                            data-send-spinner
                            class="hidden h-6 w-6"
                            viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg"
                            aria-hidden="true"
                        >
                            <circle cx="12" cy="4" r="2" fill="currentColor" />
                            <circle cx="19" cy="16" r="2" fill="currentColor" opacity=".65" />
                            <circle cx="5" cy="16" r="2" fill="currentColor" opacity=".35" />
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<div
    id="chatReportModal"
    class="fixed inset-0 z-[110] hidden items-center justify-center p-4"
    role="dialog"
    aria-modal="true"
    aria-labelledby="chatReportTitle"
>
    <div
        class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm"
        data-chat-report-close
    ></div>

    <div
        class="relative z-10 w-full max-w-md bg-white rounded-3xl shadow-2xl overflow-hidden"
    >
        <div
            class="px-6 py-4 bg-amber-50 border-b border-amber-100 flex items-center justify-between"
        >
            <h3
                id="chatReportTitle"
                class="text-lg font-bold text-amber-800"
            >
                گزارش {{ $user->name }}
            </h3>

            <button
                type="button"
                data-chat-report-close
                class="min-w-11 min-h-11 text-gray-500 hover:text-gray-700 rounded-full"
                aria-label="بستن پنجره گزارش"
            >
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form
            action="{{ route('report.store') }}"
            method="POST"
            class="p-6"
        >
            @csrf

            <input
                type="hidden"
                name="reported_id"
                value="{{ $user->id }}"
            >

            <label
                for="chat_report_reason"
                class="block text-sm font-bold text-gray-700 mb-2"
            >
                دلیل گزارش
            </label>

            <textarea
                id="chat_report_reason"
                name="reason"
                rows="4"
                maxlength="255"
                required
                class="w-full rounded-2xl border-gray-200 focus:border-amber-500 focus:ring-amber-500 resize-none"
                placeholder="لطفاً موضوع را کوتاه و روشن توضیح دهید..."
            ></textarea>

            <button
                type="submit"
                class="w-full min-h-12 mt-5 rounded-2xl bg-red-600 hover:bg-red-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-red-500 text-white font-bold transition"
            >
                ارسال گزارش
            </button>
        </form>
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

    [data-send-spinner]:not(.hidden) {
        animation: sendSpinnerRotate 0.8s linear infinite;
        transform-origin: center;
    }

    @keyframes sendSpinnerRotate {
        to {
            transform: rotate(360deg);
        }
    }

    @media (prefers-reduced-motion: reduce) {
        [data-send-spinner]:not(.hidden) {
            animation-duration: 1.6s;
        }
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

        const messageInput = document.getElementById('messageInput');
        const submitButton = document.getElementById('sendMessageButton');
        const feedback = document.getElementById('messageFeedback');
        const emojiButton = document.getElementById('emojiButton');
        const emojiPicker = document.getElementById('emojiPicker');
        let isSubmitting = false;

        const showFeedback = (message, type = 'error') => {
            if (!feedback) return;
            feedback.textContent = message;
            feedback.className = 'mb-2 rounded-xl px-3 py-2 text-sm ' +
                (type === 'success' ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700');
        };

        emojiButton?.addEventListener('click', () => emojiPicker?.classList.toggle('hidden'));
        emojiPicker?.querySelectorAll('[data-emoji]').forEach((button) => {
            button.addEventListener('click', () => {
                if (!messageInput) return;
                const start = messageInput.selectionStart ?? messageInput.value.length;
                const end = messageInput.selectionEnd ?? start;
                messageInput.setRangeText(button.dataset.emoji, start, end, 'end');
                messageInput.focus();
                emojiPicker.classList.add('hidden');
            });
        });

        const startPaymentButton =
            document.getElementById('startPaymentButton');

        const closePaymentButton =
            document.getElementById('closePaymentButton');

        const safetyMenuButton =
            document.getElementById('chatSafetyMenuButton');

        const safetyMenu =
            document.getElementById('chatSafetyMenu');

        const reportModal =
            document.getElementById('chatReportModal');

        const closeSafetyMenu = () => {
            safetyMenu?.classList.add('hidden');
            safetyMenuButton?.setAttribute(
                'aria-expanded',
                'false'
            );
        };

        const closeReportModal = () => {
            reportModal?.classList.add('hidden');
            reportModal?.classList.remove('flex');
            safetyMenuButton?.focus();
        };

        safetyMenuButton?.addEventListener(
            'click',
            (event) => {
                event.stopPropagation();

                const willOpen =
                    safetyMenu?.classList.contains('hidden');

                safetyMenu?.classList.toggle('hidden');
                safetyMenuButton.setAttribute(
                    'aria-expanded',
                    willOpen ? 'true' : 'false'
                );
            }
        );

        safetyMenu?.addEventListener(
            'click',
            (event) => event.stopPropagation()
        );

        document.addEventListener('click', closeSafetyMenu);

        document
            .querySelector('[data-chat-report-open]')
            ?.addEventListener('click', () => {
                closeSafetyMenu();
                reportModal?.classList.remove('hidden');
                reportModal?.classList.add('flex');
                document
                    .getElementById('chat_report_reason')
                    ?.focus();
            });

        document
            .querySelectorAll('[data-chat-report-close]')
            .forEach((element) => {
                element.addEventListener(
                    'click',
                    closeReportModal
                );
            });

        document.addEventListener('keydown', (event) => {
            if (event.key !== 'Escape') {
                return;
            }

            closeSafetyMenu();

            if (
                reportModal &&
                !reportModal.classList.contains('hidden')
            ) {
                closeReportModal();
            }
        });

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

            if (isSubmitting) return;

            const formData = new FormData(form);

            const messageText = messageInput?.value.trim() ?? '';
            if (!messageText) {
                showFeedback('متن پیام را وارد کنید.');
                return;
            }

            isSubmitting = true;
            submitButton.disabled = true;
            submitButton.setAttribute('aria-busy', 'true');
            submitButton.classList.add('opacity-60', 'cursor-not-allowed');
            submitButton.querySelector('[data-send-icon]')?.classList.add('hidden');
            submitButton.querySelector('[data-send-spinner]')?.classList.remove('hidden');

            const controller = new AbortController();
            const requestTimeout = window.setTimeout(
                () => controller.abort(),
                15000
            );

            try {
                const response = await fetch(
                    form.action,
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
                        signal: controller.signal,
                    }
                );

                let data = {};
                try { data = await response.json(); } catch (_) {}

                if (response.status === 422) {
                    if (data.errors) {
                        showFeedback(Object.values(data.errors)[0][0]);
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
                    const messages = {
                        401: 'نشست شما منقضی شده است؛ دوباره وارد شوید.',
                        403: 'اجازه ارسال پیام به این کاربر را ندارید.',
                        419: 'نشست امنیتی منقضی شده است؛ صفحه را تازه‌سازی کنید.',
                        429: 'تعداد درخواست‌ها زیاد است؛ کمی بعد دوباره تلاش کنید.',
                        500: 'خطای داخلی رخ داد؛ لطفاً دوباره تلاش کنید.',
                    };
                    throw new Error(messages[response.status] || data.message || 'ارسال پیام ناموفق بود.');
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
                messageInput?.focus();
                showFeedback('پیام ارسال شد.', 'success');
            } catch (error) {
                showFeedback(
                    error?.name === 'AbortError'
                        ? 'پاسخ سرور بیش از حد طول کشید. لطفاً دوباره تلاش کنید.'
                        : (error?.message || 'ارتباط با سرور برقرار نشد.')
                );
            } finally {
                window.clearTimeout(requestTimeout);
                isSubmitting = false;
                submitButton.disabled = false;
                submitButton.removeAttribute('aria-busy');
                submitButton.classList.remove('opacity-60', 'cursor-not-allowed');
                submitButton.querySelector('[data-send-icon]')?.classList.remove('hidden');
                submitButton.querySelector('[data-send-spinner]')?.classList.add('hidden');
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
