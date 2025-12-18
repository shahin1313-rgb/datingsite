@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto bg-gray-50 min-h-screen pb-20">
    <div class="sticky top-0 bg-white/80 backdrop-blur-md z-10 p-4 border-b border-gray-100 flex justify-between items-center">
        <h2 class="text-2xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-pink-500 to-orange-400">
            Messages
        </h2>
        <div class="text-gray-400">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
            </svg>
        </div>
    </div>

    <div class="p-2">
        <ul class="space-y-1">
            @forelse ($contacts as $userId => $messages)
                @php
                    $latestMessage = $messages->first();
                    $contact = $latestMessage->sender_id == auth()->id() ? $latestMessage->receiver : $latestMessage->sender;
                    $unreadCount = $unreadCounts[$userId] ?? 0;
                @endphp

                <li onclick="handleClick(event, '{{ route('messages.show', $contact->id) }}')"
                    class="relative flex items-center p-3 transition-all duration-200 active:bg-gray-200 hover:bg-white rounded-2xl cursor-pointer group">
                    
                    <div class="relative flex-shrink-0">
                        <img src="{{ asset('storage/' . $contact->profile_picture) }}" 
                             alt="{{ $contact->name }}"
                             class="w-16 h-16 rounded-full object-cover ring-2 ring-white shadow-sm">
                        @if($unreadCount > 0)
                            <span class="absolute top-0 right-0 block h-4 w-4 rounded-full ring-2 ring-white bg-gradient-to-tr from-pink-500 to-orange-400"></span>
                        @endif
                    </div>

                    <div class="ml-4 flex-1 border-b border-gray-100 pb-3 group-last:border-0">
                        <div class="flex justify-between items-baseline">
                            <h3 class="font-bold text-gray-800 text-md capitalize">{{ $contact->name }}</h3>
                            <span class="text-xs text-gray-400">{{ $latestMessage->created_at->diffForHumans(null, true) }}</span>
                        </div>
                        
                        <div class="flex justify-between items-center mt-1">
                            <p class="text-sm {{ $unreadCount > 0 ? 'text-gray-900 font-semibold' : 'text-gray-500' }} truncate w-48">
                                {{ $latestMessage->message }}
                            </p>
                            
                            <div class="flex space-x-2 stop-click">
                                @if (auth()->user()->hasBlocked($contact->id))
                                    <form method="POST" action="{{ route('user.unblock', $contact->id) }}">
                                        @csrf
                                        <button type="submit" class="text-xs font-bold text-blue-500 hover:underline">Unblock</button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('user.block', $contact->id) }}">
                                        @csrf
                                        <button type="submit" class="opacity-0 group-hover:opacity-100 text-xs font-bold text-gray-300 hover:text-red-500 transition-opacity">Block</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>

                    @if ($unreadCount > 0)
                        <div class="ml-2 bg-gradient-to-tr from-pink-500 to-orange-400 text-white text-[10px] font-bold px-2 py-1 rounded-full min-w-[20px] text-center">
                            {{ $unreadCount }}
                        </div>
                    @endif
                </li>
            @empty
                <div class="flex flex-col items-center justify-center mt-20 text-gray-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mb-4 opacity-20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                    <p>هنوز پیامی ندارید</p>
                </div>
            @endforelse
        </ul>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function handleClick(event, url) {
        if (event.target.closest('.stop-click')) {
            return;
        }
        window.location.href = url;
    }
</script>
@endpush