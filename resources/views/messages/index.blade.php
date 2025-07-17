@extends('layouts.app')

@section('content')
    <div class="max-w-3xl mx-auto p-4">
        <h2 class="text-2xl font-bold mb-4 text-center">Inbox</h2>

        <div class="bg-white shadow rounded-lg">
            <div class="p-4">
                <ul class="divide-y divide-gray-200">
                    @forelse ($contacts as $userId => $messages)
                        @php
                            $latestMessage = $messages->first();
                            $contact =
                                $latestMessage->sender_id == auth()->id()
                                    ? $latestMessage->receiver
                                    : $latestMessage->sender;
                            $unreadCount = $unreadCounts[$userId] ?? 0;
                        @endphp

                        <li onclick="handleClick(event, '{{ route('messages.show', $contact->id) }}')"
                            class="cursor-pointer hover:bg-gray-50 transition p-4 rounded-md flex justify-between items-center flex-wrap">

                            <div class="flex items-center space-x-4">
                                <img src="{{ asset('storage/' . $contact->profile_picture) }}" alt="Profile Picture"
                                    class="w-16 h-16 sm:w-20 sm:h-20 rounded-full object-cover border shadow">

                                <div>
                                    <p class="font-semibold text-gray-900 text-lg">{{ $contact->name }}</p>
                                    <p class="text-sm text-gray-500">
                                        {{ Str::limit($latestMessage->message, 50) }}
                                    </p>
                                </div>
                            </div>

                            <div class="flex flex-col items-end space-y-2 mt-4 sm:mt-0">
                                @if ($unreadCount > 0)
                                    <span class="bg-red-600 text-white text-xs px-2 py-1 rounded-full shadow">
                                        {{ $unreadCount }} Unread
                                    </span>
                                @endif

                                <a href="{{ route('messages.show', $contact->id) }}"
                                    class="stop-click bg-blue-600 hover:bg-blue-700 text-white text-sm px-3 py-1.5 rounded shadow">
                                    View Chat
                                </a>

                                @if (auth()->user()->hasBlocked($contact->id))
                                    <form method="POST" action="{{ route('user.unblock', $contact->id) }}"
                                        class="stop-click">
                                        @csrf
                                        <button type="submit"
                                            class="bg-yellow-500 hover:bg-yellow-600 text-white text-sm px-3 py-1.5 rounded shadow">
                                            Unblock
                                        </button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('user.block', $contact->id) }}"
                                        class="stop-click">
                                        @csrf
                                        <button type="submit"
                                            class="bg-red-600 hover:bg-red-700 text-white text-sm px-3 py-1.5 rounded shadow">
                                            Block
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </li>
                    @empty
                        <li class="p-4 text-center text-gray-500">No messages found.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function handleClick(event, url) {
            // جلوگیری از کلیک روی دکمه‌ها
            if (event.target.closest('.stop-click')) {
                return;
            }
            window.location.href = url;
        }
    </script>
@endpush
