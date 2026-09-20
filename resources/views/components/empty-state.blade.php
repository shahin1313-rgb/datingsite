@props([
    'title',
    'description' => null,
    'icon' => 'fa-search',
    'actionUrl' => null,
    'actionLabel' => null,
])

<section class="vlora-empty" role="status">
    <div class="max-w-md">
        <span class="mx-auto mb-5 inline-flex h-16 w-16 items-center justify-center rounded-full bg-rose-500/10 text-2xl text-rose-400" aria-hidden="true">
            <i class="fas {{ $icon }}"></i>
        </span>
        <h2 class="text-xl font-black text-white">{{ $title }}</h2>
        @if($description)
            <p class="mt-3 text-sm leading-7 text-zinc-400">{{ $description }}</p>
        @endif
        @if($actionUrl && $actionLabel)
            <a href="{{ $actionUrl }}" class="vlora-btn-primary mt-6">{{ $actionLabel }}</a>
        @endif
    </div>
</section>
