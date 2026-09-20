<!doctype html>
<html class="dark" lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->isLocale('fa') ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#09090b">
    <title>@yield('title', config('app.name', 'Vlora'))</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet">
    @yield('head')
</head>
<body>
    <a href="#main-content" class="sr-only focus:not-sr-only focus:fixed focus:right-4 focus:top-4 focus:z-[10000] focus:rounded-full focus:bg-white focus:px-4 focus:py-3 focus:text-black">
        رفتن به محتوای اصلی
    </a>

    <div id="globalLoading" class="fixed inset-0 z-[9999] flex flex-col items-center justify-center bg-zinc-950">
        <div class="relative flex h-16 w-16 items-center justify-center rounded-full border border-rose-400/25 bg-rose-500/10">
            <i class="fas fa-heart animate-pulse text-2xl text-rose-500" aria-hidden="true"></i>
        </div>
        <span class="mt-4 text-sm font-bold text-zinc-400">{{ __('ui.loading') }}</span>
    </div>

    <div id="app" x-data="{ sidebarOpen: false, langMenu: false }" class="flex min-h-screen flex-col">
        @auth
            <div x-show="sidebarOpen" x-cloak class="relative z-[1000] lg:hidden" @keydown.escape.window="sidebarOpen = false">
                <div class="fixed inset-0 bg-black/70 backdrop-blur-sm" @click="sidebarOpen = false" aria-hidden="true"></div>
                <aside
                    x-show="sidebarOpen"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="translate-x-full"
                    x-transition:enter-end="translate-x-0"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="translate-x-0"
                    x-transition:leave-end="translate-x-full"
                    class="fixed inset-y-0 right-0 w-[min(86vw,20rem)] overflow-y-auto border-l border-white/10 bg-zinc-950 p-5 shadow-2xl"
                    aria-label="منوی کاربری"
                >
                    <div class="mb-6 flex items-center gap-3 border-b border-white/10 pb-5">
                        <img src="{{ auth()->user()->profilePhotoUrl() }}" alt="{{ auth()->user()->name }}" class="h-14 w-14 rounded-2xl object-cover">
                        <div class="min-w-0 flex-1">
                            <p class="truncate font-black text-white">{{ auth()->user()->name }}</p>
                            <p class="mt-1 text-xs text-zinc-500">حساب کاربری شما</p>
                        </div>
                        <button type="button" @click="sidebarOpen = false" class="vlora-icon-action" aria-label="{{ __('ui.close_menu') }}">
                            <i class="fas fa-times" aria-hidden="true"></i>
                        </button>
                    </div>

                    <nav class="space-y-2">
                        @php
                            $drawerLinks = [
                                ['home', 'home', 'fa-fire', __('ui.explore')],
                                ['search', 'search', 'fa-search', __('ui.search')],
                                ['messages.index', 'messages.*', 'fa-comment-dots', __('ui.messages')],
                                ['likes.index', 'likes.*', 'fa-heart', __('ui.likes')],
                                ['profile.edit', 'profile.*', 'fa-user', __('ui.edit_profile')],
                                ['user.tickets.index', 'user.tickets.*', 'fa-question-circle', __('ui.support_tickets')],
                            ];
                        @endphp
                        @foreach($drawerLinks as [$routeName, $pattern, $icon, $label])
                            <a href="{{ route($routeName) }}" class="flex min-h-12 items-center gap-3 rounded-2xl px-4 text-sm font-bold transition {{ request()->routeIs($pattern) ? 'bg-rose-500 text-white' : 'text-zinc-300 hover:bg-white/5' }}">
                                <i class="fas {{ $icon }} w-5" aria-hidden="true"></i><span>{{ $label }}</span>
                            </a>
                        @endforeach
                        <a href="{{ route('premium.upgrade') }}" class="flex min-h-12 items-center gap-3 rounded-2xl border border-amber-400/20 bg-amber-400/10 px-4 text-sm font-bold text-amber-300">
                            <i class="fas fa-crown w-5" aria-hidden="true"></i><span>{{ __('ui.upgrade') }}</span>
                        </a>
                        <form method="POST" action="{{ route('logout') }}" class="pt-3">
                            @csrf
                            <button type="submit" class="flex min-h-12 w-full items-center gap-3 rounded-2xl px-4 text-sm font-bold text-red-400 hover:bg-red-500/10">
                                <i class="fas fa-sign-out-alt w-5" aria-hidden="true"></i><span>{{ __('ui.logout') }}</span>
                            </button>
                        </form>
                    </nav>
                </aside>
            </div>
        @endauth

        <header class="sticky top-0 z-50 border-b border-white/10 bg-zinc-950/80 backdrop-blur-xl">
            <div class="vlora-shell flex min-h-16 items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    @auth
                        <button type="button" @click="sidebarOpen = true" class="vlora-icon-action lg:hidden" aria-label="{{ __('ui.open_menu') }}">
                            <i class="fas fa-bars" aria-hidden="true"></i>
                        </button>
                    @endauth
                    <a href="{{ auth()->check() ? route('home') : url('/') }}" class="flex min-h-11 items-center gap-2 rounded-xl px-1" aria-label="صفحه اصلی ولورا">
                        <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-rose-400 to-rose-700 text-white shadow-lg shadow-rose-950/40"><i class="fas fa-heart" aria-hidden="true"></i></span>
                        <span class="text-xl font-black tracking-tight text-white">ولورا</span>
                    </a>
                </div>

                @auth
                    <nav class="hidden items-center gap-1 lg:flex" aria-label="ناوبری اصلی">
                        <a href="{{ route('home') }}" class="rounded-full px-4 py-2 text-sm font-bold {{ request()->routeIs('home') ? 'bg-white text-zinc-950' : 'text-zinc-400 hover:text-white' }}">{{ __('ui.explore') }}</a>
                        <a href="{{ route('search') }}" class="rounded-full px-4 py-2 text-sm font-bold {{ request()->routeIs('search') ? 'bg-white text-zinc-950' : 'text-zinc-400 hover:text-white' }}">{{ __('ui.search') }}</a>
                        <a href="{{ route('messages.index') }}" class="relative rounded-full px-4 py-2 text-sm font-bold {{ request()->routeIs('messages.*') ? 'bg-white text-zinc-950' : 'text-zinc-400 hover:text-white' }}">
                            {{ __('ui.messages') }}
                            @if(($globalUnreadCount ?? 0) > 0)<span class="absolute -left-1 -top-1 flex h-5 min-w-5 items-center justify-center rounded-full bg-rose-500 px-1 text-[10px] text-white">{{ min($globalUnreadCount, 99) }}</span>@endif
                        </a>
                        <a href="{{ route('likes.index') }}" class="rounded-full px-4 py-2 text-sm font-bold {{ request()->routeIs('likes.*') ? 'bg-white text-zinc-950' : 'text-zinc-400 hover:text-white' }}">{{ __('ui.likes') }}</a>
                    </nav>
                @endauth

                <div class="flex items-center gap-1">
                    @if(!request()->is('/') && !request()->routeIs('home'))
                        <button type="button" data-history-back class="vlora-icon-action" aria-label="{{ __('ui.back') }}"><i class="fas fa-arrow-left" aria-hidden="true"></i></button>
                    @endif
                    <div class="relative">
                        <button type="button" @click="langMenu = !langMenu" class="vlora-icon-action" aria-label="{{ __('ui.language') }}" :aria-expanded="langMenu.toString()"><i class="fas fa-globe" aria-hidden="true"></i></button>
                        <div x-show="langMenu" x-cloak @click.away="langMenu = false" class="absolute left-0 mt-2 w-36 overflow-hidden rounded-2xl border border-white/10 bg-zinc-900 p-1 shadow-2xl">
                            <a href="{{ url('lang/fa') }}" class="block rounded-xl px-3 py-2 text-sm text-zinc-200 hover:bg-white/10">فارسی</a>
                            <a href="{{ url('lang/en') }}" class="block rounded-xl px-3 py-2 text-sm text-zinc-200 hover:bg-white/10">English</a>
                            <a href="{{ url('lang/fr') }}" class="block rounded-xl px-3 py-2 text-sm text-zinc-200 hover:bg-white/10">Français</a>
                        </div>
                    </div>
                    @auth
                        <a href="{{ route('profile.edit') }}" class="hidden h-11 w-11 overflow-hidden rounded-full border border-white/10 lg:block" aria-label="{{ __('ui.edit_profile') }}">
                            <img src="{{ auth()->user()->profilePhotoUrl() }}" alt="" class="h-full w-full object-cover">
                        </a>
                        <form method="POST" action="{{ route('logout') }}" class="hidden lg:block">
                            @csrf
                            <button type="submit" class="vlora-icon-action text-red-400" aria-label="{{ __('ui.logout') }}"><i class="fas fa-sign-out-alt" aria-hidden="true"></i></button>
                        </form>
                    @endauth
                    @guest
                        <a href="{{ route('login') }}" class="vlora-btn-secondary hidden sm:inline-flex">ورود</a>
                    @endguest
                </div>
            </div>
        </header>

        <main id="main-content" class="main-content flex-1 @auth pb-safe sm:pb-8 @endauth">
            <div data-app-content-container class="w-full max-w-7xl mx-auto">@yield('content')</div>
        </main>

        @auth
            <nav class="fixed inset-x-0 bottom-0 z-50 border-t border-white/10 bg-zinc-950/90 px-2 pb-[env(safe-area-inset-bottom)] backdrop-blur-xl sm:hidden" aria-label="ناوبری موبایل">
                <div class="mx-auto grid h-16 max-w-md grid-cols-4">
                    @php
                        $mobileLinks = [
                            ['home', 'home', 'fa-fire', __('ui.explore')],
                            ['search', 'search', 'fa-search', __('ui.search')],
                            ['messages.index', 'messages.*', 'fa-comment-dots', __('ui.messages')],
                            ['dashboard', 'dashboard', 'fa-user', __('ui.profile')],
                        ];
                    @endphp
                    @foreach($mobileLinks as [$routeName, $pattern, $icon, $label])
                        <a href="{{ route($routeName) }}" class="relative flex min-h-12 flex-col items-center justify-center gap-1 text-[10px] font-bold {{ request()->routeIs($pattern) ? 'text-rose-400' : 'text-zinc-500' }}" @if(request()->routeIs($pattern)) aria-current="page" @endif>
                            <i class="fas {{ $icon }} text-lg" aria-hidden="true"></i><span>{{ $label }}</span>
                            @if($routeName === 'messages.index' && ($globalUnreadCount ?? 0) > 0)<span class="absolute left-1/4 top-1 flex h-4 min-w-4 items-center justify-center rounded-full bg-rose-500 px-1 text-[9px] text-white">{{ min($globalUnreadCount, 99) }}</span>@endif
                        </a>
                    @endforeach
                </div>
            </nav>
        @endauth
    </div>
    @stack('scripts')
</body>
</html>
