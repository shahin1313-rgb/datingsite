<!doctype html>
<html class="dark" lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#09090b">
    <title>ولورا | آشنایی امن و معنادار</title>
    @vite('resources/css/app.css')
    <link href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet">
</head>
<body>
    <header class="sticky top-0 z-50 border-b border-white/10 bg-zinc-950/80 backdrop-blur-xl">
        <nav class="vlora-shell flex min-h-16 items-center justify-between" aria-label="ناوبری اصلی">
            <a href="{{ url('/') }}" class="flex min-h-11 items-center gap-2" aria-label="صفحه اصلی ولورا">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-rose-400 to-rose-700 text-white"><i class="fas fa-heart" aria-hidden="true"></i></span>
                <span class="text-xl font-black text-white">ولورا</span>
            </a>
            <div class="flex items-center gap-2">
                <a href="{{ route('login') }}" class="vlora-btn-secondary">ورود</a>
                <a href="{{ route('register') }}" class="vlora-btn-primary hidden sm:inline-flex">ثبت‌نام رایگان</a>
            </div>
        </nav>
    </header>

    <main>
        <section class="relative overflow-hidden border-b border-white/10 py-16 sm:py-24">
            <div class="pointer-events-none absolute -right-28 top-0 h-96 w-96 rounded-full bg-rose-600/20 blur-3xl" aria-hidden="true"></div>
            <div class="vlora-shell relative grid items-center gap-12 lg:grid-cols-[1.05fr_.95fr]">
                <div>
                    <span class="inline-flex rounded-full border border-rose-400/20 bg-rose-500/10 px-4 py-2 text-xs font-bold text-rose-300">آشنایی واقعی، انتخاب آگاهانه</span>
                    <h1 class="mt-6 max-w-3xl text-4xl font-black leading-[1.35] text-white sm:text-6xl">یک شروع ساده برای یک رابطهٔ معنادار</h1>
                    <p class="mt-5 max-w-2xl text-base leading-8 text-zinc-400 sm:text-lg">پروفایل‌های واقعی را بررسی کنید، بر اساس معیارهای خود جست‌وجو کنید و در محیطی روشن و امن گفت‌وگو را آغاز کنید.</p>
                    <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                        <a href="{{ route('register') }}" class="vlora-btn-primary px-7">ساخت حساب رایگان <i class="fas fa-arrow-left" aria-hidden="true"></i></a>
                        <a href="{{ route('login') }}" class="vlora-btn-secondary px-7">قبلاً عضو شده‌ام</a>
                    </div>
                    <p class="mt-4 text-xs leading-6 text-zinc-500">با ثبت‌نام، قوانین و حریم خصوصی سرویس را می‌پذیرید.</p>
                </div>

                <div class="vlora-panel relative mx-auto w-full max-w-md p-3 sm:p-4" aria-label="پیش‌نمایش تجربه ولورا">
                    <div class="relative min-h-[30rem] overflow-hidden rounded-[1.4rem] bg-gradient-to-br from-rose-950 via-zinc-900 to-black p-6">
                        <div class="absolute inset-0 opacity-35" style="background-image: radial-gradient(circle at 20% 20%, #fb7185 0, transparent 30%), radial-gradient(circle at 80% 70%, #be123c 0, transparent 28%);"></div>
                        <div class="relative flex h-full min-h-[27rem] flex-col justify-between">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-black text-white">پیشنهادهای منتخب</span>
                                <span class="flex h-10 w-10 items-center justify-center rounded-full bg-black/30 text-zinc-300"><i class="fas fa-sliders" aria-hidden="true"></i></span>
                            </div>
                            <div class="rounded-[1.4rem] border border-white/10 bg-black/35 p-5 backdrop-blur-md">
                                <p class="text-xs font-bold text-rose-300">بر اساس انتخاب‌های شما</p>
                                <h2 class="mt-2 text-2xl font-black text-white">گفت‌وگو را از شناخت شروع کنید</h2>
                                <p class="mt-3 text-sm leading-7 text-zinc-300">اطلاعات اصلی، علایق و هدف رابطه پیش از شروع مکالمه شفاف است.</p>
                                <div class="mt-5 grid grid-cols-[1fr_auto] gap-3">
                                    <span class="vlora-btn-secondary">مشاهده پروفایل</span>
                                    <span class="vlora-icon-action bg-rose-500"><i class="fas fa-heart" aria-hidden="true"></i></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="vlora-shell py-16 sm:py-20" aria-labelledby="benefits-title">
            <div class="mx-auto max-w-2xl text-center">
                <p class="text-sm font-bold text-rose-400">طراحی‌شده برای اعتماد</p>
                <h2 id="benefits-title" class="mt-2 text-3xl font-black text-white">کمتر حدس بزنید، بهتر انتخاب کنید</h2>
            </div>
            <div class="mt-9 grid gap-4 md:grid-cols-3">
                @foreach([
                    ['fa-search', 'جست‌وجوی دقیق', 'شهر، بازهٔ سنی و علایق را مشخص کنید و فقط نتیجه‌های مرتبط را ببینید.'],
                    ['fa-shield-alt', 'کنترل و امنیت', 'مسدودسازی، گزارش و کنترل حریم خصوصی همیشه در دسترس شماست.'],
                    ['fa-comment-dots', 'گفت‌وگوی ساده', 'پیام‌ها، وضعیت خوانده‌شدن و ارتباط‌ها در یک تجربهٔ یکپارچه قرار دارند.'],
                ] as [$icon, $title, $description])
                    <article class="vlora-panel p-6">
                        <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-rose-500/10 text-rose-400"><i class="fas {{ $icon }}" aria-hidden="true"></i></span>
                        <h3 class="mt-5 text-lg font-black text-white">{{ $title }}</h3>
                        <p class="mt-3 text-sm leading-7 text-zinc-400">{{ $description }}</p>
                    </article>
                @endforeach
            </div>
        </section>

        <section class="border-y border-white/10 bg-white/[0.025] py-12">
            <div class="vlora-shell grid gap-4 text-center sm:grid-cols-3">
                <div><strong class="block text-3xl font-black text-white">{{ number_format($totalMessages) }}</strong><span class="mt-2 block text-sm text-zinc-500">پیام ثبت‌شده</span></div>
                <div><strong class="block text-3xl font-black text-white">{{ number_format($monthlyMessages) }}</strong><span class="mt-2 block text-sm text-zinc-500">پیام در این ماه</span></div>
                <div><strong class="block text-3xl font-black text-white">{{ number_format($todayMessages) }}</strong><span class="mt-2 block text-sm text-zinc-500">پیام امروز</span></div>
            </div>
        </section>

        <section class="vlora-shell py-16 text-center sm:py-24">
            <h2 class="text-3xl font-black text-white sm:text-4xl">برای آشنایی بهتر آماده‌اید؟</h2>
            <p class="mx-auto mt-4 max-w-xl text-sm leading-7 text-zinc-400">حساب خود را بسازید، پروفایل را کامل کنید و با انتخاب‌های واقعی شروع کنید.</p>
            <a href="{{ route('register') }}" class="vlora-btn-primary mt-7 px-8">شروع رایگان</a>
        </section>
    </main>

    <footer class="border-t border-white/10 py-8">
        <div class="vlora-shell flex flex-col items-center justify-between gap-4 text-center text-xs text-zinc-500 sm:flex-row sm:text-right">
            <p>© {{ now()->year }} ولورا. همهٔ حقوق محفوظ است.</p>
            <div class="flex gap-5"><a href="{{ route('register') }}" class="hover:text-white">ساخت حساب</a><a href="{{ route('login') }}" class="hover:text-white">ورود</a></div>
        </div>
    </footer>
</body>
</html>
