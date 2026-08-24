@extends('layouts.app')

@section('content')
<div
    dir="rtl"
    class="relative min-h-[calc(100vh-3.5rem)] overflow-hidden bg-gradient-to-br from-rose-50 via-white to-pink-50 dark:from-slate-950 dark:via-slate-900 dark:to-slate-950"
>
    {{-- تزئینات پس‌زمینه --}}
    <div
        class="pointer-events-none absolute -right-24 -top-24 h-72 w-72 rounded-full bg-pink-200/40 blur-3xl dark:bg-pink-900/20"
    ></div>

    <div
        class="pointer-events-none absolute -bottom-32 -left-24 h-80 w-80 rounded-full bg-rose-200/40 blur-3xl dark:bg-rose-900/20"
    ></div>

    <div
        class="relative z-10 mx-auto flex min-h-[calc(100vh-3.5rem)] w-full max-w-6xl items-center justify-center px-4 py-10 sm:px-6"
    >
        <div
            class="w-full max-w-lg overflow-hidden rounded-3xl border border-pink-100 bg-white/95 shadow-2xl shadow-pink-100/60 backdrop-blur-xl dark:border-slate-700 dark:bg-slate-900/95 dark:shadow-none"
        >
            {{-- نوار رنگی بالای کارت --}}
            <div
                class="h-1.5 w-full bg-gradient-to-l from-pink-500 via-rose-500 to-fuchsia-500"
            ></div>

            <div class="px-6 py-8 sm:px-10 sm:py-10">
                {{-- آیکون ایمیل --}}
                <div class="mb-6 flex justify-center">
                    <div class="relative">
                        <div
                            class="absolute inset-0 scale-125 rounded-full bg-pink-100 opacity-70 blur-lg dark:bg-pink-900/30"
                        ></div>

                        <div
                            class="relative flex h-24 w-24 items-center justify-center rounded-full bg-gradient-to-br from-pink-500 to-rose-500 shadow-xl shadow-pink-200 dark:shadow-none"
                        >
                            <i
                                class="fas fa-envelope-open-text text-4xl text-white"
                                aria-hidden="true"
                            ></i>
                        </div>

                        <div
                            class="absolute -bottom-1 -right-1 flex h-8 w-8 items-center justify-center rounded-full border-4 border-white bg-emerald-500 text-white dark:border-slate-900"
                        >
                            <i
                                class="fas fa-check text-xs"
                                aria-hidden="true"
                            ></i>
                        </div>
                    </div>
                </div>

                {{-- عنوان --}}
                <div class="text-center">
                    <span
                        class="mb-3 inline-flex items-center gap-2 rounded-full bg-pink-50 px-3 py-1 text-xs font-bold text-pink-600 dark:bg-pink-950/40 dark:text-pink-300"
                    >
                        <i
                            class="fas fa-shield-alt"
                            aria-hidden="true"
                        ></i>

                        امنیت حساب کاربری
                    </span>

                    <h1
                        class="text-2xl font-black text-gray-900 dark:text-white sm:text-3xl"
                    >
                        ایمیل خود را تأیید کنید
                    </h1>

                    <p
                        class="mx-auto mt-3 max-w-md text-sm leading-7 text-gray-500 dark:text-slate-400"
                    >
                        لینک فعال‌سازی حساب برای ایمیل زیر ارسال شده است.
                        برای ادامه فعالیت در ولورا، روی لینک موجود در ایمیل
                        کلیک کنید.
                    </p>
                </div>

                {{-- نمایش ایمیل --}}
                <div
                    class="mt-6 flex items-center gap-3 rounded-2xl border border-pink-100 bg-pink-50/70 p-4 dark:border-pink-900/40 dark:bg-pink-950/20"
                >
                    <div
                        class="flex h-11 w-11 flex-shrink-0 items-center justify-center rounded-xl bg-white text-pink-500 shadow-sm dark:bg-slate-800"
                    >
                        <i
                            class="fas fa-at"
                            aria-hidden="true"
                        ></i>
                    </div>

                    <div class="min-w-0 flex-1">
                        <p
                            class="text-xs font-semibold text-gray-500 dark:text-slate-400"
                        >
                            ایمیل ثبت‌شده
                        </p>

                        <p
                            dir="ltr"
                            class="mt-1 truncate text-left text-sm font-bold text-gray-800 dark:text-slate-200"
                            title="{{ auth()->user()->email }}"
                        >
                            {{ auth()->user()->email }}
                        </p>
                    </div>
                </div>

                {{-- پیام ارسال مجدد موفق --}}
                @if (session('resent'))
                    <div
                        role="alert"
                        aria-live="polite"
                        class="mt-5 flex items-start gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-700 dark:border-emerald-900/50 dark:bg-emerald-950/30 dark:text-emerald-300"
                    >
                        <div
                            class="mt-0.5 flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full bg-emerald-500 text-white"
                        >
                            <i
                                class="fas fa-check text-xs"
                                aria-hidden="true"
                            ></i>
                        </div>

                        <div>
                            <p class="text-sm font-black">
                                ایمیل جدید ارسال شد
                            </p>

                            <p class="mt-1 text-xs leading-6">
                                لینک تأیید جدید برای شما ارسال شد.
                                پوشه Inbox و Spam را بررسی کنید.
                            </p>
                        </div>
                    </div>
                @endif

                {{-- نمایش خطاها --}}
                @if ($errors->any())
                    <div
                        role="alert"
                        class="mt-5 rounded-2xl border border-rose-200 bg-rose-50 p-4 text-sm font-semibold text-rose-700 dark:border-rose-900/50 dark:bg-rose-950/30 dark:text-rose-300"
                    >
                        <div class="flex items-start gap-2">
                            <i
                                class="fas fa-exclamation-circle mt-1"
                                aria-hidden="true"
                            ></i>

                            <span>
                                ارسال ایمیل انجام نشد. لطفاً چند لحظه دیگر
                                دوباره تلاش کنید.
                            </span>
                        </div>
                    </div>
                @endif

                {{-- مراحل راهنما --}}
                <div class="mt-7 rounded-2xl bg-gray-50 p-4 dark:bg-slate-800/60">
                    <p
                        class="mb-4 text-sm font-black text-gray-800 dark:text-white"
                    >
                        مراحل فعال‌سازی حساب
                    </p>

                    <div class="space-y-4">
                        <div class="flex items-start gap-3">
                            <span
                                class="flex h-7 w-7 flex-shrink-0 items-center justify-center rounded-full bg-pink-500 text-xs font-black text-white"
                            >
                                ۱
                            </span>

                            <div>
                                <p
                                    class="text-sm font-bold text-gray-700 dark:text-slate-200"
                                >
                                    صندوق ورودی ایمیل را باز کنید
                                </p>

                                <p
                                    class="mt-1 text-xs leading-5 text-gray-500 dark:text-slate-400"
                                >
                                    ایمیلی با عنوان تأیید آدرس ایمیل از ولورا
                                    پیدا کنید.
                                </p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <span
                                class="flex h-7 w-7 flex-shrink-0 items-center justify-center rounded-full bg-rose-500 text-xs font-black text-white"
                            >
                                ۲
                            </span>

                            <div>
                                <p
                                    class="text-sm font-bold text-gray-700 dark:text-slate-200"
                                >
                                    روی دکمه تأیید ایمیل کلیک کنید
                                </p>

                                <p
                                    class="mt-1 text-xs leading-5 text-gray-500 dark:text-slate-400"
                                >
                                    لینک تأیید دارای اعتبار محدود است؛ بهتر است
                                    همین حالا آن را باز کنید.
                                </p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <span
                                class="flex h-7 w-7 flex-shrink-0 items-center justify-center rounded-full bg-fuchsia-500 text-xs font-black text-white"
                            >
                                ۳
                            </span>

                            <div>
                                <p
                                    class="text-sm font-bold text-gray-700 dark:text-slate-200"
                                >
                                    وارد حساب کاربری شوید
                                </p>

                                <p
                                    class="mt-1 text-xs leading-5 text-gray-500 dark:text-slate-400"
                                >
                                    پس از تأیید، دسترسی به داشبورد و امکانات
                                    ولورا فعال می‌شود.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ارسال مجدد --}}
                <div
                    class="mt-7"
                    x-data="{ submitting: false }"
                >
                    <p
                        class="mb-3 text-center text-xs leading-6 text-gray-500 dark:text-slate-400"
                    >
                        ایمیل را دریافت نکردید؟ ابتدا پوشه Spam را بررسی کنید،
                        سپس لینک جدید درخواست کنید.
                    </p>

                    <form
                        method="POST"
                        action="{{ route('verification.resend') }}"
                        @submit="submitting = true"
                    >
                        @csrf

                        <button
                            type="submit"
                            :disabled="submitting"
                            class="flex w-full items-center justify-center gap-2 rounded-2xl bg-gradient-to-l from-pink-500 to-rose-500 px-5 py-3.5 text-sm font-black text-white shadow-lg shadow-pink-200 transition duration-200 hover:-translate-y-0.5 hover:shadow-xl focus:outline-none focus:ring-4 focus:ring-pink-200 disabled:cursor-not-allowed disabled:opacity-60 disabled:hover:translate-y-0 dark:shadow-none dark:focus:ring-pink-900/40"
                        >
                            <i
                                x-show="!submitting"
                                class="fas fa-paper-plane"
                                aria-hidden="true"
                            ></i>

                            <i
                                x-show="submitting"
                                x-cloak
                                class="fas fa-spinner fa-spin"
                                aria-hidden="true"
                            ></i>

                            <span x-show="!submitting">
                                ارسال مجدد لینک تأیید
                            </span>

                            <span
                                x-show="submitting"
                                x-cloak
                            >
                                در حال ارسال ایمیل...
                            </span>
                        </button>
                    </form>
                </div>

                {{-- خروج از حساب --}}
                <div
                    class="mt-5 border-t border-gray-100 pt-5 text-center dark:border-slate-800"
                >
                    <p
                        class="mb-3 text-xs text-gray-400 dark:text-slate-500"
                    >
                        ایمیل واردشده متعلق به شما نیست؟
                    </p>

                    <form
                        method="POST"
                        action="{{ route('logout') }}"
                        class="inline"
                    >
                        @csrf

                        <button
                            type="submit"
                            class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-sm font-bold text-gray-500 transition hover:bg-gray-100 hover:text-rose-500 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-rose-400"
                        >
                            <i
                                class="fas fa-sign-out-alt"
                                aria-hidden="true"
                            ></i>

                            خروج و استفاده از ایمیل دیگر
                        </button>
                    </form>
                </div>

                {{-- پیام امنیتی --}}
                <div
                    class="mt-6 flex items-center justify-center gap-2 text-[11px] text-gray-400 dark:text-slate-500"
                >
                    <i
                        class="fas fa-lock"
                        aria-hidden="true"
                    ></i>

                    <span>
                        تأیید ایمیل برای امنیت حساب شما ضروری است.
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection