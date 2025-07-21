@extends('adminlte::page')
@extends('adminlte::page')

@section('title', 'جزئیات تیکت')

@section('content')
    <div class="max-w-5xl mx-auto p-8 bg-gradient-to-br from-white to-gray-50 shadow-xl rounded-2xl border border-gray-100">
        <!-- عنوان تیکت -->
        <h2 class="text-4xl font-extrabold text-gray-900 mb-6 flex items-center gap-2">
            <i class="fa fa-ticket-alt text-blue-600"></i>
            <span>جزئیات تیکت</span>
        </h2>

        <!-- اطلاعات تیکت -->
        <div class="bg-white p-6 rounded-xl border border-gray-200 shadow hover:shadow-lg transition mb-8">
            <h3 class="text-2xl font-semibold text-gray-800">{{ $ticket->subject }}</h3>
            <p class="text-gray-600 mt-3 leading-relaxed">{{ $ticket->message }}</p>
            <div class="mt-4 text-sm text-gray-500 flex items-center gap-5 flex-wrap">
                <span class="flex items-center gap-1">
                    <i class="fa fa-user text-gray-400"></i> {{ $ticket->user->name ?? 'کاربر حذف‌شده' }}
                </span>
                <span class="flex items-center gap-1">
                    <i class="fa fa-clock text-gray-400"></i> {{ $ticket->created_at->format('Y-m-d H:i') }}
                </span>
                <span
                    class="px-3 py-1 rounded-full text-xs font-medium
                    {{ $ticket->status === 'closed' ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }}">
                    {{ $ticket->status === 'closed' ? 'بسته' : 'باز' }}
                </span>
            </div>
        </div>

        <!-- پاسخ‌ها -->
        <div class="mb-8">
            <h3 class="text-2xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                <i class="fa fa-comments text-blue-600"></i> پاسخ‌ها
            </h3>

            @if ($ticket->replies->isEmpty())
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-6 text-gray-500 text-center shadow-sm">
                    پاسخی وجود ندارد.
                </div>
            @else
                <div class="space-y-5">
                    @foreach ($ticket->replies as $reply)
                        <div class="bg-gray-50 rounded-xl p-5 shadow border border-gray-200 hover:shadow-md transition">
                            <div class="flex justify-between items-center">
                                <span class="font-semibold text-gray-900 flex items-center gap-1">
                                    <i class="fa fa-user-circle text-gray-500"></i>
                                    {{ $reply->user->name ?? 'کاربر حذف‌شده' }}
                                </span>
                                <span class="text-sm text-gray-500">{{ $reply->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="mt-3 text-gray-700 leading-relaxed">{{ $reply->message }}</p>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- فرم پاسخ -->
        @if ($ticket->status !== 'closed')
            <div class="bg-white border-t border-gray-200 pt-6 shadow-inner rounded-lg p-6">
                <h3 class="text-xl font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <i class="fa fa-reply text-blue-600"></i> پاسخ ادمین
                </h3>
                <form action="{{ route('admin.tickets.reply', $ticket->id) }}" method="POST" class="space-y-5">
                    @csrf
                    <textarea name="message" rows="4"
                        class="w-full p-4 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-400 shadow-sm"
                        placeholder="متن پاسخ خود را وارد کنید..." required></textarea>
                    <div class="flex gap-3">
                        <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg shadow transition">
                            ارسال پاسخ
                        </button>
                        <form action="{{ route('admin.tickets.close', $ticket->id) }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="bg-red-500 hover:bg-red-600 text-white px-5 py-2 rounded-lg shadow transition">
                                بستن تیکت
                            </button>
                        </form>
                    </div>
                </form>
            </div>
        @else
            <div class="bg-red-50 text-red-700 p-4 rounded-lg border border-red-200 text-center shadow-sm">
                این تیکت بسته شده است.
            </div>
        @endif
    </div>
@endsection
