@extends('layouts.app')

<div id="loadingSpinner" role="status">
    <span class="visually-hidden">Loading...</span>
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
    <div class="container">
        <div class="chat-container container-fluid d-flex flex-column vh-100">
            <div class="header p-3 bg-primary text-white">
                <div class="d-flex align-items-center">
                    <img src="{{ asset('storage/' . $user->profile_picture) }}" class="user-avatar me-3"
                        alt="{{ $user->name }}'s profile picture">
                    <h4 class="mb-0">{{ $user->name }}</h4>
                    <h6 class="mb-0"></h6>
                </div>
            </div>

            <div class="message-container flex-grow-1" style="overflow-y: auto;">
                @foreach ($messages as $message)
                    <div
                        class="message-bubble {{ $message->sender_id == auth()->id() ? 'sent-message' : 'received-message' }}">
                        <img src="{{ asset('storage/' . $message->sender->profile_picture) }}" class="user-avatar"
                            alt="{{ $message->sender->name }}'s profile picture">
                        <div class="message-content">
                            <p class="mb-0">{{ $message->message }}</p>
                            <span class="timestamp"></span>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="message-input p-2 border-top">
                <form action="{{ route('messages.store', $user) }}" method="POST" class="d-flex">
                    @csrf
                    <div class="input-group">
                        <textarea name="message" class="form-control" rows="1" placeholder="Type a message..."
                            style="resize: none; max-height: 150px;" required></textarea>
                        <button type="submit" class="btn btn-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                class="bi bi-send" viewBox="0 0 16 16">
                                <path
                                    d="M15.854.146a.5.5 0 0 1 .11.54l-5.819 14.547a.5.5 0 0 1-.933.003L5.034 9.747l-4.493 1.923a.5.5 0 0 1-.65-.65l1.925-4.492L.633 1.01a.5.5 0 0 1 .006-.933L15.314.036a.5.5 0 0 1 .54.11z" />
                            </svg>
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <script>
            // Auto-scroll to bottom of chat
            const messageContainer = document.querySelector('.message-container');
            messageContainer.scrollTop = messageContainer.scrollHeight;

            // Auto-expand textarea
            const textarea = document.querySelector('textarea');
            textarea.addEventListener('input', function() {
                this.style.height = 'auto';
                this.style.height = this.scrollHeight + 'px';
            });
        </script>
    </div>
@endsection
