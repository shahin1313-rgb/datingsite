@extends('layouts.app')

@section('content')
<div
    class="min-h-screen w-full bg-gray-50 flex items-center justify-center p-4"
    dir="rtl"
>
    <div
        class="bg-white rounded-3xl shadow-xl p-6 text-center max-w-md w-full border border-gray-100"
    >
        <h2 class="text-xl font-black text-gray-800 mb-2">
            ارتقای حساب با USDT واقعی
        </h2>

        <p class="text-sm text-gray-500 mb-5">
            برای فعال‌شدن Premium به مدت
            {{ $payment['premium_days'] }}
            روز، مبلغ دقیق و اختصاصی این فاکتور را فقط روی BSC Mainnet بفرستید.
        </p>

        <div
            class="mb-5 p-3 bg-amber-50 text-amber-800 text-xs rounded-xl text-right border border-amber-200 leading-6"
        >
            <strong>هشدار مهم:</strong>

            شبکه‌های Sepolia، Ethereum، BSC Testnet، TRC20 و
            سایر شبکه‌ها پذیرفته نمی‌شوند. ارسال روی شبکه اشتباه
            ممکن است باعث از دست‌رفتن دارایی شود.
        </div>

        <div
            class="mb-5 p-3 bg-blue-50 text-blue-800 text-xs rounded-xl text-right border border-blue-200 leading-6"
        >
            این مبلغ فقط به حساب شما متصل است و تا ساعت
            <strong dir="ltr">
                {{ $payment['expires_at']->format('H:i') }}
            </strong>
            معتبر خواهد بود. مبلغ را گرد نکنید و دقیقاً همان عدد
            نمایش‌داده‌شده را انتقال دهید.
        </div>

        @if ($errors->any())
            <div
                class="mb-4 p-3 bg-red-50 text-red-600 text-xs rounded-xl text-right"
            >
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('success'))
            <div
                class="mb-4 p-3 bg-green-50 text-green-600 text-xs rounded-xl text-right font-bold"
            >
                {{ session('success') }}
            </div>
        @endif

        <div
            class="bg-gray-50 rounded-2xl p-5 mb-6 border border-gray-100"
        >
            <div class="grid grid-cols-2 gap-3 mb-5 text-xs">
                <div
                    class="bg-white rounded-xl p-3 border border-gray-200"
                >
                    <span class="block text-gray-400 mb-1">
                        شبکه
                    </span>

                    <strong class="text-gray-800">
                        {{ $payment['network'] }}
                    </strong>
                </div>

                <div
                    class="bg-white rounded-xl p-3 border border-gray-200"
                >
                    <span class="block text-gray-400 mb-1">
                        مبلغ پرداخت
                    </span>

                    <div class="flex items-center justify-center gap-1" dir="ltr">
                        <input
                            type="text"
                            readonly
                            id="paymentAmount"
                            value="{{ $payment['amount'] }}"
                            class="w-28 bg-transparent text-pink-600 font-bold text-center focus:outline-none cursor-pointer"
                            aria-label="مبلغ دقیق پرداخت"
                        >

                        <strong class="text-pink-600">
                            {{ $payment['asset'] }}
                        </strong>

                        <button
                            type="button"
                            id="copyAmountButton"
                            class="text-gray-400 hover:text-pink-600 p-1 transition"
                            aria-label="کپی مبلغ دقیق"
                        >
                            <i class="fa fa-copy" aria-hidden="true"></i>
                        </button>
                    </div>

                    <span
                        id="copyAmountMessage"
                        class="hidden block text-[10px] text-green-600 mt-1 font-bold"
                    >
                        مبلغ کپی شد!
                    </span>
                </div>
            </div>

            <div class="flex justify-center mb-4">
                <img
                    src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&amp;data={{ rawurlencode($payment['wallet_address']) }}"
                    alt="QR Code آدرس ولت BSC"
                    class="rounded-xl shadow-sm border p-1 bg-white"
                    width="150"
                    height="150"
                >
            </div>

            <p class="text-xs text-gray-400 mb-2">
                آدرس دریافت USDT در BSC Mainnet:
            </p>

            <div class="relative flex items-center">
                <input
                    type="text"
                    readonly
                    id="walletAddress"
                    value="{{ $payment['wallet_address'] }}"
                    dir="ltr"
                    class="w-full bg-white p-3 pl-10 rounded-xl text-xs font-mono border border-gray-200 text-gray-700 text-center select-all focus:outline-none cursor-pointer"
                >

                <button
                    type="button"
                    id="copyWalletButton"
                    class="absolute left-2 text-gray-400 hover:text-pink-600 p-1.5 transition"
                    aria-label="کپی آدرس ولت"
                >
                    <i
                        class="fa fa-copy text-sm"
                        aria-hidden="true"
                    ></i>
                </button>
            </div>

            <span
                id="copyMessage"
                class="hidden block text-xs text-green-600 mt-2 font-bold"
            >
                کپی شد!
            </span>

            <p class="text-[11px] text-gray-400 mt-4">
                فعال‌سازی پس از حداقل
                {{ $payment['confirmations'] }}
                تأیید شبکه انجام می‌شود.
            </p>
        </div>

        <form
            action="{{ route('premium.verifyCrypto') }}"
            method="POST"
            class="space-y-4"
        >
            @csrf

            <input
                type="hidden"
                name="payment_intent"
                value="{{ $payment['intent_public_id'] }}"
            >

            <div class="text-right">
                <label
                    for="transaction_hash"
                    class="block text-xs font-bold text-gray-600 mb-2"
                >
                    هش تراکنش BSC (TxHash):
                </label>

                <input
                    id="transaction_hash"
                    type="text"
                    name="transaction_hash"
                    required
                    maxlength="66"
                    autocomplete="off"
                    spellcheck="false"
                    dir="ltr"
                    value="{{ old('transaction_hash') }}"
                    placeholder="0x..."
                    class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-pink-500 text-sm font-mono text-center"
                >
            </div>

            <button
                type="submit"
                class="w-full bg-gradient-to-r from-pink-600 to-purple-600 text-white font-bold py-3 px-4 rounded-xl shadow-lg hover:shadow-pink-500/20 hover:opacity-95 transition-all"
            >
                بررسی تراکنش و فعال‌سازی Premium
            </button>
        </form>
    </div>
</div>

<script nonce="{{ \Illuminate\Support\Facades\Vite::cspNonce() }}">
    document
        .getElementById('copyWalletButton')
        ?.addEventListener('click', () => {
            copyValue('walletAddress', 'copyMessage');
        });

    document
        .getElementById('copyAmountButton')
        ?.addEventListener('click', () => {
            copyValue('paymentAmount', 'copyAmountMessage');
        });

    function copyValue(inputId, messageId) {
        const input = document.getElementById(inputId);

        const copyMessage = document.getElementById(messageId);

        navigator.clipboard
            .writeText(input.value)
            .then(() => {
                copyMessage.classList.remove('hidden');

                window.setTimeout(() => {
                    copyMessage.classList.add('hidden');
                }, 2000);
            })
            .catch(() => {
                input.select();
                document.execCommand('copy');

                copyMessage.classList.remove('hidden');

                window.setTimeout(() => {
                    copyMessage.classList.add('hidden');
                }, 2000);
            });
    }
</script>
@endsection
