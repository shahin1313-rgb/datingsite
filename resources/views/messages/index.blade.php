@extends('layouts.app')

@section('content')
<div class="vlora-shell py-6 sm:py-10">
    <header class="mb-7 flex items-end justify-between gap-4">
        <div>
            <p class="mb-2 text-sm font-bold text-rose-400">ارتباط‌های شما</p>
            <h1 class="text-3xl font-black text-white">{{ __('ui.messages') }}</h1>
        </div>
        <a href="{{ route('search') }}" class="vlora-icon-action" aria-label="پیدا کردن فرد جدید"><i class="fas fa-search" aria-hidden="true"></i></a>
    </header>

    @if (session('success'))
        <div class="mb-6 rounded-2xl border border-green-500/20 bg-green-500/10 p-4 text-sm text-green-300" role="status">{{ session('success') }}</div>
    @endif

    @if($contacts->isEmpty())
        <x-empty-state
            title="هنوز پیامی ندارید"
            description="پروفایل‌های پیشنهادی را ببینید و وقتی آماده بودید گفت‌وگو را شروع کنید."
            icon="fa-comment-dots"
            action-url="{{ route('home') }}"
            action-label="مشاهده پروفایل‌ها"
        />
    @else
        <section class="vlora-panel overflow-hidden" aria-label="فهرست گفت‌وگوها">
            <ul class="divide-y divide-white/10">
                @foreach ($contacts as $latestMessage)
                    @php
                        $contact = $latestMessage->sender_id == auth()->id() ? $latestMessage->receiver : $latestMessage->sender;
                        $unreadCount = $unreadCounts[$contact->id] ?? 0;
                        $latestMessageIsPrivateForCurrentUser = $latestMessage->status === 'private' && (int) $latestMessage->receiver_id === (int) auth()->id();
                    @endphp
                    <li data-chat-url="{{ route('messages.show', $contact->id) }}" class="group relative flex cursor-pointer items-center gap-3 p-4 transition hover:bg-white/[0.04] sm:gap-4 sm:p-5">
                        <div class="relative shrink-0">
                            <img src="{{ $contact->profilePhotoUrl() }}" alt="تصویر {{ $contact->name }}" class="h-14 w-14 rounded-2xl object-cover sm:h-16 sm:w-16" loading="lazy">
                            @if($unreadCount > 0)<span class="absolute -left-1 -top-1 h-3.5 w-3.5 rounded-full border-2 border-zinc-900 bg-rose-500" aria-label="پیام خوانده‌نشده"></span>@endif
                        </div>

                        <div class="min-w-0 flex-1">
                            <div class="flex items-center justify-between gap-3">
                                <h2 class="truncate font-black text-white">{{ $contact->name }}</h2>
                                <time class="shrink-0 text-[11px] text-zinc-500" datetime="{{ $latestMessage->created_at->toIso8601String() }}">{{ $latestMessage->created_at->diffForHumans(null, true) }}</time>
                            </div>
                            <div class="mt-1 flex items-center gap-2">
                                <p class="min-w-0 flex-1 truncate text-sm {{ $unreadCount > 0 ? 'font-bold text-zinc-200' : 'text-zinc-500' }}">
                                    @if($latestMessageIsPrivateForCurrentUser)<i class="fas fa-lock ml-1" aria-hidden="true"></i>پیام ویژه قفل است@else{{ $latestMessage->message }}@endif
                                </p>
                                @if($unreadCount > 0)<span class="flex h-6 min-w-6 items-center justify-center rounded-full bg-rose-500 px-1.5 text-[10px] font-black text-white">{{ $unreadCount }}</span>@endif
                            </div>
                        </div>

                        <div class="stop-click shrink-0">
                            @if(auth()->user()->hasBlocked($contact->id))
                                <form method="POST" action="{{ route('user.unblock', $contact->id) }}">@csrf<button type="submit" data-mobile-block-action class="flex h-11 w-11 items-center justify-center rounded-full text-sky-400 sm:w-auto sm:px-3" aria-label="رفع مسدودی {{ $contact->name }}"><i class="fas fa-user-check sm:ml-2" aria-hidden="true"></i><span class="hidden text-xs font-bold sm:inline">رفع مسدودی</span></button></form>
                            @else
                                <form method="POST" action="{{ route('user.block', $contact->id) }}" data-sweet-block data-block-name="{{ $contact->name }}" data-confirm="آیا از مسدود کردن این کاربر مطمئن هستید؟">@csrf<button type="submit" data-mobile-block-action class="flex h-11 w-11 items-center justify-center rounded-full text-zinc-500 hover:text-red-400 sm:w-auto sm:px-3" aria-label="مسدود کردن {{ $contact->name }}"><i class="fas fa-user-slash sm:ml-2" aria-hidden="true"></i><span class="hidden text-xs font-bold sm:inline">مسدود کردن</span></button></form>
                            @endif
                        </div>
                    </li>
                @endforeach
            </ul>
        </section>

        <div class="custom-pagination mt-8">{{ $contacts->links() }}</div>
    @endif
</div>
@endsection

@push('scripts')
<script nonce="{{ \Illuminate\Support\Facades\Vite::cspNonce() }}">
    document.addEventListener('click', (event) => {
        const chatItem = event.target.closest('[data-chat-url]');
        if (!chatItem || event.target.closest('.stop-click')) return;
        window.location.href = chatItem.dataset.chatUrl;
    });
</script>
@endpush
