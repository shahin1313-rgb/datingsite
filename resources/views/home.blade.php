@extends('layouts.app')

@section('content')
<div class="vlora-shell py-6 sm:py-10">
    @if (session('status'))
        <div class="mb-6 rounded-2xl border border-green-500/20 bg-green-500/10 p-4 text-sm text-green-300" role="status">
            {{ session('status') }}
        </div>
    @endif

    <section class="mb-8 grid items-end gap-5 md:grid-cols-[1fr_auto]">
        <div>
            <p class="mb-2 text-sm font-bold text-rose-400">{{ __('ui.explore') }}</p>
            <h1 class="max-w-2xl text-3xl font-black leading-tight text-white sm:text-5xl">
                {{ __('ui.find_match') }}
            </h1>
            <p class="mt-3 max-w-xl text-sm leading-7 text-zinc-400 sm:text-base">{{ __('ui.search_subtitle') }}</p>
        </div>
        <a href="{{ route('search') }}" class="vlora-btn-secondary w-full md:w-auto">
            <i class="fas fa-sliders" aria-hidden="true"></i>
            {{ __('ui.advanced_search') }}
        </a>
    </section>

    <section aria-labelledby="profiles-heading">
        <div class="mb-5 flex items-center justify-between">
            <h2 id="profiles-heading" class="text-lg font-black text-white">پروفایل‌های تازه</h2>
            <span class="text-xs text-zinc-500">بر اساس اطلاعات واقعی کاربران</span>
        </div>

        @if ($profiles->isEmpty())
            <x-empty-state
                title="{{ __('ui.no_results') }}"
                description="فیلترهای جست‌وجو را تغییر دهید یا کمی بعد دوباره بررسی کنید."
                action-url="{{ route('search') }}"
                action-label="{{ __('ui.advanced_search') }}"
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
