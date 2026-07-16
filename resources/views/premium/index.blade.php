@extends('layouts.app') {{-- یا هر نامی که برای لایوت اصلی دارید --}}

@section('content')
<div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100 text-center" 
     x-data="cryptoPayment()">
    
    <h2 class="text-xl font-bold text-gray-800 mb-2">Upgrade to Premium</h2>
    <p class="text-sm text-gray-500 mb-6">Get noticed faster and chat without limits.</p>

    <!-- قیمت و ویژگی‌ها -->
    <div class="bg-pink-50/50 rounded-2xl p-5 mb-6 border border-pink-100">
        <span class="text-xs font-bold uppercase tracking-wider text-pink-600 bg-pink-100 px-2.5 py-1 rounded-full">VIP Plan</span>
        <div class="mt-4 mb-4">
            <span class="text-3xl font-extrabold text-gray-900">$9.99</span>
            <span class="text-gray-500 text-sm">/ mo</span>
            <div class="text-xs text-amber-600 font-medium mt-1">(or equivalent in Crypto)</div>
        </div>

        <ul class="text-right space-y-3 max-w-xs mx-auto text-sm text-gray-600">
            <li class="flex items-center justify-between">
                <span><i class="fa fa-check-circle text-green-500 text-lg"></i></span>
                <span>Unlimited Messages</span>
            </li>
            <li class="flex items-center justify-between">
                <span><i class="fa fa-check-circle text-green-500 text-lg"></i></span>
                <span>See who liked you</span>
            </li>
            <li class="flex items-center justify-between">
                <span><i class="fa fa-check-circle text-green-500 text-lg"></i></span>
                <span>Undo accidental skips</span>
            </li>
            <li class="flex items-center justify-between">
                <span><i class="fa fa-check-circle text-green-500 text-lg"></i></span>
                <span>Profile boost once a week</span>
            </li>
        </ul>
    </div>

    <!-- بخش دکمه‌های پرداخت کریپتویی -->
    <div class="space-y-3">
        <!-- دکمه اتصال کیف پول / پرداخت -->
        <button @click="payWithCrypto()" 
                :disabled="loading"
                class="w-full bg-gradient-to-r from-pink-600 to-purple-600 hover:from-pink-700 hover:to-purple-700 text-white font-bold py-3 px-4 rounded-xl transition-all shadow-md flex items-center justify-center gap-2 disabled:opacity-50">
            <i class="fa-solid fa-wallet text-lg" x-show="!loading"></i>
            <i class="fa fa-spinner animate-spin" x-show="loading" x-cloak></i>
            <span x-text="buttonText">Choose Plan (Pay with Crypto)</span>
        </button>

        <!-- نمایش آدرس کانتراکت یا وضعیت در صورت نیاز -->
        <p x-show="statusMessage" x-text="statusMessage" class="text-xs mt-2" :class="statusClass" x-cloak></p>
    </div>
</div>

@push('scripts')
<!-- لود کردن کتابخانه Web3.js برای تعامل با متامسک -->
<script src="https://cdn.jsdelivr.net/npm/web3@4.1.1/dist/web3.min.js"></script>

<script>
function cryptoPayment() {
    return {
        loading: false,
        buttonText: 'Pay with Crypto / Connect Wallet',
        statusMessage: '',
        statusClass: 'text-gray-500',
        
        // آدرس ولت شما (دریافت‌کننده وجه) یا آدرس کانتراکت
        merchantAddress: '0x0a2F71b27902621f3E45356b96018e2358Ce8f89', 
        // مبلغ معادل به اتر (مثلاً برای $9.99 حدود 0.003 اتریوم - ترجیحاً از api بگیرید)
        amountInEth: '0.003', 

        async payWithCrypto() {
            if (typeof window.ethereum === 'undefined') {
                this.statusMessage = 'لطفاً ابتدا افزونه MetaMask را روی مرورگر خود نصب کنید.';
                this.statusClass = 'text-red-500';
                return;
            }

            this.loading = true;
            this.buttonText = 'در حال ارتباط با کیف پول...';
            
            try {
                // درخواست اتصال به کیف پول
                const web3 = new Web3(window.ethereum);
                const accounts = await window.ethereum.request({ method: 'eth_requestAccounts' });
                const userAddress = accounts[0];

                this.buttonText = 'در انتظار تایید تراکنش...';

                // ایجاد تراکنش ارسال اتریوم
                const transactionParameters = {
                    to: this.merchantAddress,
                    from: userAddress,
                    value: web3.utils.toHex(web3.utils.toWei(this.amountInEth, 'ether')),
                };

                // ارسال تراکنش به متامسک کاربر جهت تایید و امضا
                const txHash = await window.ethereum.request({
                    method: 'eth_sendTransaction',
                    params: [transactionParameters],
                });

                this.statusMessage = 'تراکنش ارسال شد! در حال تایید در شبکه...';
                this.statusClass = 'text-amber-500';

                // ارسال TxHash به بک‌اند لاراول برای تایید نهایی و ارتقای حساب
                this.verifyOnBackend(txHash);

            } catch (error) {
                console.error(error);
                this.loading = false;
                this.buttonText = 'Pay with Crypto / Connect Wallet';
                this.statusMessage = 'عملیات پرداخت توسط کاربر لغو شد یا خطایی رخ داد.';
                this.statusClass = 'text-red-500';
            }
        },

        verifyOnBackend(txHash) {
            // ارسال با fetch به لاراول
            fetch("{{ route('premium.verifyCrypto') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ transaction_hash: txHash })
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    this.statusMessage = 'حساب شما با موفقیت به ویژه ارتقا یافت!';
                    this.statusClass = 'text-green-500 font-bold';
                    this.buttonText = 'اکانت شما ویژه است';
                    setTimeout(() => {
                        window.location.href = "{{ route('dashboard') }}";
                    }, 2000);
                } else {
                    this.statusMessage = 'خطا در تایید تراکنش: ' + data.message;
                    this.statusClass = 'text-red-500';
                    this.loading = false;
                }
            })
            .catch(err => {
                this.statusMessage = 'خطای شبکه در اتصال به سرور.';
                this.statusClass = 'text-red-500';
                this.loading = false;
            });
        }
    }
}
</script>
@endpush
@endsection