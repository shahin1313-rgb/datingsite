@extends('adminlte::page')

@section('content')
    <div class="max-w-7xl mx-auto mt-10 p-6 bg-white rounded-xl shadow-md">
        <h2 class="text-3xl font-bold text-gray-800 mb-8 flex items-center gap-2">
            <i class="fa fa-ticket-alt text-pink-600"></i>
            مدیریت تیکت‌ها
        </h2>

        @if ($tickets->isEmpty())
            <div class="text-center text-gray-500 text-lg">
                هیچ تیکتی یافت نشد.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full border border-gray-200 rounded-lg overflow-hidden">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-3 text-right text-gray-600">#</th>
                            <th class="px-4 py-3 text-right text-gray-600">کاربر</th>
                            <th class="px-4 py-3 text-right text-gray-600">موضوع</th>
                            <th class="px-4 py-3 text-right text-gray-600">وضعیت</th>
                            <th class="px-4 py-3 text-right text-gray-600">تاریخ</th>
                            <th class="px-4 py-3 text-right text-gray-600">عملیات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($tickets as $ticket)
                            <tr class="border-b hover:bg-gray-50 transition">
                                <td class="px-4 py-3">{{ $ticket->id }}</td>
                                <td class="px-4 py-3">{{ $ticket->user->name ?? 'کاربر حذف‌شده' }}</td>
                                <td class="px-4 py-3 font-semibold text-gray-800">{{ $ticket->subject }}</td>
                                <td class="px-4 py-3">
                                    @if ($ticket->status === 'open')
                                        <span class="bg-green-100 text-green-700 px-2 py-1 rounded-full text-sm">باز</span>
                                    @else
                                        <span class="bg-red-100 text-red-700 px-2 py-1 rounded-full text-sm">بسته</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-gray-500 text-sm">{{ $ticket->created_at->format('Y-m-d H:i') }}
                                </td>
                                <td class="px-4 py-3 flex gap-2">
                                    <a href="{{ route('admin.tickets.show', $ticket->id) }}"
                                        class="px-3 py-1 bg-blue-500 hover:bg-blue-600 text-white rounded-lg text-sm transition">
                                        مشاهده
                                    </a>

                                    @if ($ticket->status === 'open')
                                        <form action="{{ route('admin.tickets.close', $ticket->id) }}" method="POST"
                                            onsubmit="return confirm('آیا از بستن این تیکت مطمئن هستید؟')">
                                            @csrf
                                            <button type="submit"
                                                class="px-3 py-1 bg-red-500 hover:bg-red-600 text-white rounded-lg text-sm transition">
                                                بستن
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                {{ $tickets->links('pagination::tailwind') }}
            </div>
        @endif
    </div>
@endsection
