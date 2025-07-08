@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Inbox</h2>

        <div class="card">
            <div class="card-body">
                <ul class="list-group">
                    @forelse ($contacts as $userId => $messages)
                        @php
                            $latestMessage = $messages->first();
                            $contact =
                                $latestMessage->sender_id == auth()->id()
                                    ? $latestMessage->receiver
                                    : $latestMessage->sender;
                            $unreadCount = $unreadCounts[$userId] ?? 0;
                        @endphp

                        <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                            <div class="d-flex align-items-center mb-2">
                                <img src="{{ asset('storage/' . $contact->profile_picture) }}" alt="Profile Picture"
                                    class="rounded-circle" style="width: 40px; height: 40px; margin-right: 10px;">
                                <div>
                                    <strong>{{ $contact->name }}</strong>
                                    <p class="mb-1 text-muted">{{ Str::limit($latestMessage->message, 50) }}</p>
                                </div>
                            </div>

                            <div class="d-flex align-items-center gap-2">
                                @if ($unreadCount > 0)
                                    <span class="badge bg-danger">{{ $unreadCount }} Unread</span>
                                @endif

                                <a href="{{ route('messages.show', $contact->id) }}" class="btn btn-primary btn-sm">View
                                    Chat</a>

                                {{-- Block/Unblock button for each contact --}}
                                @if (auth()->user()->hasBlocked($contact->id))
                                    <form method="POST" action="{{ route('user.unblock', $contact->id) }}">
                                        @csrf
                                        <button type="submit" class="btn btn-warning btn-sm">Unblock</button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('user.block', $contact->id) }}">
                                        @csrf
                                        <button type="submit" class="btn btn-danger btn-sm">Block</button>
                                    </form>
                                @endif
                            </div>
                        </li>
                    @empty
                        <li class="list-group-item">No messages found.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
@endsection
