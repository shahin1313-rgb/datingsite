@extends('layouts.app')

{{-- لودینگ اسپینر --}}
<div id="loadingSpinner" role="status" class="fixed inset-0 bg-white flex items-center justify-center z-50">
    <span class="text-gray-600 text-lg"></span>
</div>

<script>
    window.addEventListener('load', function() {
        const loadingOverlay = document.getElementById('loadingSpinner');
        if (loadingOverlay) {
            loadingOverlay.style.display = 'none';
        }
    });
</script>

@section('content')
    <div class="w-full max-w-5xl mx-auto h-screen px-2">
        <div class="flex flex-col h-full border rounded shadow-lg overflow-hidden">

            {{-- هدر چت --}}
            <div class="flex items-center bg-blue-600 text-white px-4 py-3">
                <img src="{{ asset('storage/' . $user->profile_picture) }}" class="w-12 h-12 rounded-full object-cover mr-3"
                    alt="{{ $user->name }}'s profile picture">
                <div>
                    <h4 class="text-lg font-semibold">{{ $user->name }}</h4>
                </div>
            </div>

            {{-- لیست پیام‌ها --}}
            <div class="flex-1 overflow-y-auto p-4 space-y-4 bg-gray-50">
                @foreach ($messages as $message)
                    @php
                        $isOwn = $message->sender_id == auth()->id();
                    @endphp

                    <div class="w-full flex {{ $isOwn ? 'justify-end' : 'justify-start' }}">
                        <div class="flex items-start gap-3 {{ $isOwn ? 'flex-row-reverse' : '' }}">
                            <!-- آواتار -->
                            <img src="{{ asset('storage/' . $message->sender->profile_picture) }}"
                                class="w-10 h-10 rounded-full object-cover"
                                alt="{{ $message->sender->name }}'s profile picture">

                            <!-- باکس پیام -->
                            <div
                                class="max-w-xs md:max-w-md p-3 rounded-lg shadow
                    {{ $isOwn ? 'bg-blue-100 text-right' : 'bg-white text-left' }}">
                                <p class="text-sm text-gray-800">{{ $message->message }}</p>
                                <span class="text-xs text-gray-500 block mt-1">
                                    {{ $message->created_at->diffForHumans() }}
                                </span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>


            {{-- فرم ارسال پیام --}}
            <!-- باکس ارسال پیام -->
            <div class="fixed bottom-0 left-0 w-full bg-white border-t shadow p-4 z-50">
                <form action="{{ route('messages.store', $user) }}" method="POST" class="flex items-center gap-2">
                    @csrf
                    <textarea name="message" rows="1" placeholder="پیام خود را بنویسید..."
                        class="flex-grow resize-none border rounded-lg p-2 text-sm focus:outline-none focus:ring focus:border-blue-300"
                        required></textarea>
                    <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow text-sm">
                        ارسال
                    </button>
                </form>
            </div>

        </div>
    </div>

    {{-- اسکرول خودکار به انتهای پیام‌ها --}}
    <script>
        const messageContainer = document.querySelector('.overflow-y-auto');
        messageContainer.scrollTop = messageContainer.scrollHeight;

        const textarea = document.querySelector('textarea');
        textarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = this.scrollHeight + 'px';
        });
    </script>
@endsection
