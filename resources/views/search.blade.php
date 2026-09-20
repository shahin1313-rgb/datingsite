@extends('layouts.app')

@section('content')
<div class="vlora-shell py-6 sm:py-10">
    <header class="mb-7">
        <p class="mb-2 text-sm font-bold text-rose-400">جست‌وجوی دقیق</p>
        <h1 class="text-3xl font-black text-white sm:text-4xl">{{ __('ui.find_match') }}</h1>
        <p class="mt-3 text-sm leading-7 text-zinc-400">{{ __('ui.search_subtitle') }}</p>
    </header>

    @if ($errors->any())
        <div class="mb-6 rounded-2xl border border-red-500/25 bg-red-500/10 p-4 text-red-200" role="alert">
            <p class="font-bold">{{ __('ui.search_errors') }}</p>
            <ul class="mt-2 list-inside list-disc space-y-1 text-sm">
                @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <section class="vlora-panel mb-9 p-4 sm:p-7" aria-label="فیلترهای جست‌وجو">
        <x-search-form />
    </section>

    <section aria-labelledby="search-results-heading">
        <div class="mb-5 flex items-center justify-between gap-4">
            <h2 id="search-results-heading" class="text-lg font-black text-white">نتیجه‌های جست‌وجو</h2>
            @if(request()->query())
                <a href="{{ route('search') }}" class="text-sm font-bold text-rose-400 hover:text-rose-300">پاک‌کردن فیلترها</a>
            @endif
        </div>

        @if ($profiles->isEmpty())
            <x-empty-state
                title="{{ __('ui.no_results') }}"
                description="فیلترها را کمی گسترده‌تر کنید و دوباره جست‌وجو کنید."
                action-url="{{ route('search') }}"
                action-label="پاک‌کردن فیلترها"
            />
        @else
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($profiles as $profile)
                    <x-profile-card :profile="$profile" />
                @endforeach
            </div>
            <div class="custom-pagination mt-10">
                {{ $profiles->appends(request()->query())->links() }}
            </div>
        @endif
    </section>
</div>
@endsection
