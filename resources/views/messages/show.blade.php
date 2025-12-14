@extends('layouts.app')

@section('content')

{{-- لودینگ --}}
<div id="loadingSpinner" class="fixed inset-0 bg-white flex items-center justify-center z-50">
    <svg class="animate-spin h-12 w-12 text-blue-600" viewBox="0 0 24 24"></svg>
</div>

<script>
window.addEventListener('load', () => {
    document.getElementById('loadingSpinner')?.remove();
});
</script>

<div class="fixed inset-x-0 top-16 bottom-0 flex flex-col bg-white">

    {{-- هدر چت --}}
    <div class="flex items-center bg-blue-600 text-white px-4 py-3 shrink-0">
        <a href="{{ url()->previous() }}" class="mr-3">←</a>

        <img src="{{ $user->profile_picture
            ? asset('storage/'.$user->profile_picture)
            : 'https://ui-avatars.com/api/?name='.urlencode($user->name) }}"
             class="w-10 h-10 rounded-full mr-3">

        <div>
            <div class="font-semibold">{{ $user->name }}</div>
        </div>
    </div>

    {{-- پیام‌ها --}}
    <div id="messagesContainer" class="flex-1 overflow-y-auto p-4 bg-gray-50">
        @foreach($messages as $message)
            @php $isOwn = $message->sender_id === auth()->id(); @endphp

            <div class="flex {{ $isOwn ? 'justify-end' : 'justify-start' }} mb-3">
                 {{-- پیام خصوصی و کاربر گیرنده --}}
    @if ($message->status === 'private' && ! $isOwn)
        <div class="px-4 py-3 rounded-xl max-w-xs bg-gray-100 border text-sm text-gray-600">
            🔒 برای مشاهده این پیام، <br>
            <strong>حداقل یکی از دو طرف</strong> باید دارای
            <strong>اکانت پریمیوم</strong> باشد.
        </div>

    {{-- پیام واقعی --}}
    @else
        <div class="px-4 py-2 rounded-xl max-w-xs
            {{ $isOwn ? 'bg-blue-500 text-white' : 'bg-white border' }}">
            {{ $message->message }}
        </div>
    @endif
    @if ($message->status === 'private' && ! $isOwn)
    <div class="mt-2 text-center">
        <a href="{{ route('premium.upgrade') }}"
           class="text-blue-600 text-sm font-medium">
            ارتقا به اکانت پریمیوم
        </a>
    </div>
@endif

        @endforeach
        <div id="end-of-messages"></div>
    </div>

    {{-- فرم ارسال پیام (AJAX ONLY) --}}
    <div class="border-t p-4">
        <form id="sendMessageForm" class="flex gap-3">
            @csrf
            <input type="hidden" name="receiver_id" value="{{ $user->id }}">

            <textarea name="message"
                      rows="1"
                      class="flex-1 border rounded-xl px-3 py-2"
                      placeholder="پیام خود را بنویسید..."
                      required></textarea>

            <button type="submit"
                    class="bg-blue-600 text-white px-4 py-2 rounded-xl">
                ارسال
            </button>
        </form>
    </div>
</div>

{{-- مدال پرداخت --}}
<div id="paymentModal"
     class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">

    <div class="bg-white rounded-xl p-6 w-full max-w-sm text-center">
        <h3 class="text-lg font-semibold mb-4">
            برای ارسال پیام بیشتر، پرداخت لازم است
        </h3>

        <button onclick="startPayment()"
                class="bg-green-600 text-white px-6 py-2 rounded-lg">
            پرداخت
        </button>

        <button onclick="closePaymentModal()"
                class="block mt-3 text-gray-500 text-sm mx-auto">
            انصراف
        </button>
    </div>
</div>

{{-- اسکریپت‌ها --}}
<script>
let currentReceiver = null;

document.getElementById('sendMessageForm').addEventListener('submit', function (e) {
    e.preventDefault();

    const formData = new FormData(this);

    fetch("{{ route('messages.store') }}", {
        method: "POST",
        headers: {
            'X-CSRF-TOKEN': document.querySelector('input[name=_token]').value,
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.error === 'PAYMENT_REQUIRED') {
            currentReceiver = data.receiver_id;
            openPaymentModal();
        } else {
            location.reload();
        }
    })
    .catch(console.error);
});

function openPaymentModal() {
    document.getElementById('paymentModal').classList.remove('hidden');
    document.getElementById('paymentModal').classList.add('flex');
}

function closePaymentModal() {
    document.getElementById('paymentModal').classList.add('hidden');
    document.getElementById('paymentModal').classList.remove('flex');
}

function startPayment() {
    fetch("{{ route('payments.create') }}", {
        method: "POST",
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ receiver_id: currentReceiver })
    })
    .then(res => res.json())
    .then(data => window.location.href = data.payment_url);
}
</script>

@endsection
