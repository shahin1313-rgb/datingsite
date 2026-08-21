@extends('layouts.app')

@section('content')
<!-- تغییر کلاس‌های دیو اصلی -->
<div class="fixed inset-0 h-screen w-screen bg-gray-50 flex items-center justify-center p-4 overflow-hidden">        <div class="bg-white rounded-3xl shadow-xl p-6 text-center max-w-md w-full border border-gray-100">
        
        <h2 class="text-xl font-black text-gray-800 mb-2">ارتقای حساب با کریپتو</h2>
        <p class="text-sm text-gray-500 mb-6">مبلغ مورد نظر را به ولت زیر واریز کرده و هش تراکنش را ثبت کنید.</p>

        <!-- نمایش پیام‌های خطا یا موفقیت لاراول -->
        @if ($errors->any())
            <div class="mb-4 p-3 bg-red-50 text-red-600 text-xs rounded-xl text-right">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('success'))
            <div class="mb-4 p-3 bg-green-50 text-green-600 text-xs rounded-xl text-right font-bold">
                {{ session('success') }}
            </div>
        @endif

        <!-- نمایش آدرس ولت و کیوآر کد -->
        <div class="bg-gray-50 rounded-2xl p-5 mb-6 border border-gray-100">
            <div class="flex justify-center mb-4">
                <!-- تصویر QR Code آدرس ولت شما -->
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=0x0a2F71b27902621f3E45356b96018e2358Ce8f89" 
                     alt="QR Code" 
                     class="rounded-xl shadow-sm border p-1 bg-white">
            </div>
            
            <p class="text-xs text-gray-400 mb-2">آدرس ولت ما (شبکه Sepolia یا Ethereum):</p>
            
            <!-- باکس آدرس ولت همراه با قابلیت کپی با کلیک -->
            <div class="relative flex items-center">
                <input type="text" readonly id="walletAddress" value="0x0a2F71b27902621f3E45356b96018e2358Ce8f89" 
                       class="w-full bg-white p-3 pl-10 rounded-xl text-xs font-mono border border-gray-200 text-gray-700 text-center select-all focus:outline-none cursor-pointer">
                <button type="button" onclick="copyWalletAddress()" class="absolute left-2 text-gray-400 hover:text-pink-600 p-1.5 transition">
                    <i class="fa fa-copy text-sm"></i>
                </button>
            </div>
            <span id="copyMessage" class="hidden text-xs text-green-600 mt-1 font-bold">کپی شد!</span>
            
            <div class="text-base font-black text-pink-600 mt-4">مبلغ واریزی: 0.003 ETH</div>
        </div>

        <!-- فرم ثبت تراکنش -->
        <form action="{{ route('premium.verifyCrypto') }}" method="POST" class="space-y-4">
            @csrf
            <div class="text-right">
                <label class="block text-xs font-bold text-gray-600 mb-2">هش تراکنش (Transaction Hash / TxID):</label>
                <input type="text" 
                       name="transaction_hash" 
                       required 
                       value="{{ old('transaction_hash') }}"
                       placeholder="0x..." 
                       class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-pink-500 text-sm font-mono text-center">
            </div>

            <button type="submit" class="w-full bg-gradient-to-r from-pink-600 to-purple-600 text-white font-bold py-3 px-4 rounded-xl shadow-lg hover:shadow-pink-500/20 hover:opacity-95 transition-all">
                ثبت و ارتقای حساب
            </button>
        </form>
    </div>
</div>

<script>
    // اسکریپت ساده برای کپی کردن آدرس ولت در کلیپ‌بورد کاربر
    function copyWalletAddress() {
        const copyText = document.getElementById("walletAddress");
        copyText.select();
        copyText.setSelectionRange(0, 99999); // برای موبایل

        navigator.clipboard.writeText(copyText.value).then(() => {
            const message = document.getElementById("copyMessage");
            message.classList.remove("hidden");
            setTimeout(() => {
                message.classList.add("hidden");
            }, 2000);
        });
    }
</script>
@endsection