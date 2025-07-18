<div class="max-w-3xl mx-auto mt-10">
    <h2 class="text-2xl font-bold mb-6">لیست تیکت‌های من</h2>

    @foreach ($tickets as $ticket)
        <div class="bg-white p-4 rounded shadow mb-4">
            <h3 class="font-semibold">{{ $ticket->subject }}</h3>
            <p class="text-gray-700">{{ $ticket->message }}</p>
            <span class="text-sm text-gray-500">{{ $ticket->created_at->diffForHumans() }}</span>
            <span
                class="ml-2 px-2 py-1 rounded-full text-sm {{ $ticket->status === 'open' ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-600' }}">
                {{ $ticket->status === 'open' ? 'باز' : 'بسته' }}
            </span>
        </div>
    @endforeach
</div>
