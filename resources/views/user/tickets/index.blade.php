<div class="container mx-auto px-4 py-8">
    <!-- بخش ارسال تیکت -->
    <div class="max-w-xl mx-auto bg-white p-6 rounded shadow mb-10">
        <h2 class="text-xl font-bold mb-4">ارسال تیکت پشتیبانی</h2>
        <form action="{{ route('user.tickets.store') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block mb-1">موضوع</label>
                <input type="text" name="subject" class="w-full border p-2 rounded" required>
            </div>
            <div class="mb-4">
                <label class="block mb-1">پیام</label>
                <textarea name="message" class="w-full border p-2 rounded" rows="5" required></textarea>
            </div>
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                ارسال
            </button>
        </form>
    </div>

    <!-- بخش لیست تیکت‌ها -->
    <div class="max-w-3xl mx-auto">
        <h2 class="text-2xl font-bold mb-6">لیست تیکت‌های من</h2>

        @forelse ($tickets as $ticket)
            <div class="bg-white p-4 rounded shadow mb-4">
                <h3 class="font-semibold">{{ $ticket->subject }}</h3>
                <p class="text-gray-700 mt-2">{{ $ticket->message }}</p>
                <div class="mt-2 flex items-center text-sm text-gray-500">
                    <span>{{ $ticket->created_at->diffForHumans() }}</span>
                    <span
                        class="ml-2 px-2 py-1 rounded-full text-sm {{ $ticket->status === 'open' ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-600' }}">
                        {{ $ticket->status === 'open' ? 'باز' : 'بسته' }}
                    </span>
                </div>
            </div>
        @empty
            <p class="text-gray-500">هیچ تیکتی ثبت نشده است.</p>
        @endforelse
    </div>
</div>
