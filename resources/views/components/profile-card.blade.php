@props(['profile'])

<article class="vlora-profile-card group">
    <img
        src="{{ $profile->profilePhotoUrl() }}"
        alt="تصویر پروفایل {{ $profile->name }}"
        class="vlora-profile-card__image"
        loading="lazy"
        width="640"
        height="800"
    >

    <div class="absolute inset-x-0 top-0 flex items-start justify-between p-4">
        @if($profile->isOnline())
            <span class="inline-flex min-h-8 items-center gap-2 rounded-full border border-white/10 bg-black/60 px-3 text-xs font-bold text-white backdrop-blur-md">
                <span class="h-2 w-2 rounded-full bg-green-400" aria-hidden="true"></span>
                {{ __('ui.online') }}
            </span>
        @else
            <span></span>
        @endif

        <a
            href="{{ route('profile.show', $profile->id) }}"
            class="vlora-icon-action"
            aria-label="{{ __('ui.view_profile') }}: {{ $profile->name }}"
        >
            <i class="fas fa-arrow-left" aria-hidden="true"></i>
        </a>
    </div>

    <div class="absolute inset-x-0 bottom-0 p-5 sm:p-6">
        <div class="mb-3 flex flex-wrap items-center gap-2">
            <h2 class="text-2xl font-black text-white">{{ $profile->name }}</h2>
            @if($profile->age)
                <span class="text-lg font-bold text-zinc-200">{{ $profile->age }}</span>
            @endif
        </div>

        <div class="mb-4 flex flex-wrap items-center gap-2 text-xs font-bold text-zinc-200">
            @if($profile->city)
                <span class="rounded-full border border-white/10 bg-white/10 px-3 py-1.5 backdrop-blur-md">
                    <i class="fas fa-map-marker-alt ml-1 text-rose-400" aria-hidden="true"></i>
                    {{ $profile->city }}
                </span>
            @endif
        </div>

        <p class="mb-5 line-clamp-2 min-h-10 text-sm leading-7 text-zinc-300">
            {{ $profile->bio ?: __('ui.no_bio') }}
        </p>

        <div class="grid grid-cols-[1fr_auto] gap-3">
            <a href="{{ route('profile.show', $profile->id) }}" class="vlora-btn-secondary">
                {{ __('ui.view_profile') }}
            </a>
            <a
                href="{{ route('messages.show', $profile) }}"
                class="vlora-icon-action border-rose-400/30 bg-rose-500 text-white"
                aria-label="{{ __('ui.send_message') }} به {{ $profile->name }}"
            >
                <i class="fas fa-comment-dots" aria-hidden="true"></i>
            </a>
        </div>
    </div>
</article>
