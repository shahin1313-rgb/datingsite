@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-12 transition-colors duration-300">
    <div class="flex justify-center">
        <div class="w-full md:w-2/3 lg:w-1/2">
            <div class="bg-white dark:bg-slate-900 shadow-2xl rounded-3xl overflow-hidden border border-pink-100 dark:border-slate-800 transition-all duration-300">
                
                <div class="h-32 bg-gradient-to-r from-pink-400 to-rose-400 dark:from-pink-600 dark:to-rose-600"></div>

                <div class="relative px-6 pb-6">
                    <div class="flex justify-center">
                        @if ($user->profile_picture)
                            <img src="{{ asset('storage/' . $user->profile_picture) }}"
                                 class="w-40 h-40 rounded-full border-4 border-white dark:border-slate-900 shadow-lg object-cover -mt-20 bg-white dark:bg-slate-800"
                                 alt="Profile Picture">
                        @else
                            <div class="w-40 h-40 rounded-full bg-pink-50 dark:bg-slate-800 border-4 border-white dark:border-slate-900 shadow-lg -mt-20 flex items-center justify-center text-pink-300 dark:text-slate-600 z-10">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-20 w-20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                        @endif
                    </div>

                   <div class="text-center mt-4">
                        <h2 class="text-3xl font-extrabold text-gray-800 dark:text-slate-100">{{ $user->name }}</h2>
                        <p class="text-pink-500 dark:text-pink-400 font-medium flex justify-center items-center gap-1 mt-1 mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            {{ $user->city }}
                        </p>
                        
                        <div class="flex items-center justify-center gap-3 max-w-sm mx-auto">
                            
                            <a href="/messages/{{ $user->id }}" 
                               class="flex-1 text-center bg-gradient-to-r from-pink-500 to-rose-500 hover:from-pink-600 hover:to-rose-600 text-white font-bold py-3 px-4 rounded-2xl shadow-lg shadow-pink-200 dark:shadow-none transition duration-300 transform hover:-translate-y-0.5 flex items-center justify-center gap-2 text-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                </svg>
                                ارسال پیام
                            </a>

                           @if(auth()->check() && auth()->id() !== $user->id)
                                <form id="like-form" action="{{ route('like.store', ['likedUserId' => $user->id]) }}" method="POST" class="flex-shrink-0" onsubmit="ajaxLike(event)">
                                    @csrf
                                    
                                    @php
                                        $hasLiked = \DB::table('likes')
                                            ->where('user_id', auth()->id())
                                            ->where('liked_user_id', $user->id)
                                            ->exists();
                                    @endphp

                                    <button type="submit" id="like-btn"
                                            class="p-3 rounded-2xl shadow-lg transition duration-300 transform hover:-translate-y-0.5 flex items-center justify-center w-12 h-12 {{ $hasLiked ? 'bg-red-500 text-white shadow-red-200 dark:shadow-none hover:bg-red-600' : 'bg-pink-50 dark:bg-slate-800 text-pink-600 dark:text-pink-400 border border-pink-200 dark:border-slate-700 hover:bg-pink-600 hover:text-white dark:hover:bg-pink-600 dark:hover:text-white' }}"
                                            title="{{ $hasLiked ? 'برداشتن لایک' : 'لایک کردن' }}">
                                        <i id="like-icon" class="{{ $hasLiked ? 'fa' : 'far' }} fa-heart text-xl"></i>
                                    </button>
                                </form>
                            @endif

                        </div>
                    </div>

                    @if (session('status'))
                        <div class="mt-4 bg-green-50 dark:bg-green-950/40 border-r-4 border-green-500 text-green-700 dark:text-green-400 p-4 rounded-xl text-sm" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                   
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-8 text-sm text-gray-600 dark:text-slate-300 bg-pink-50/50 dark:bg-slate-800/50 p-5 rounded-2xl border border-transparent dark:border-slate-800">
                        
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-pink-600 dark:text-pink-400 text-base">💖 علاقه‌مند به:</span>
                            <span>{{ $user->interested_in }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-pink-600 dark:text-pink-400 text-base">💰 درآمد:</span>
                            <span>{{ $user->salary }} میلیون تومان</span>
                        </div>
                        
                        <div class="col-span-1 md:col-span-2 mt-2 border-t border-pink-100 dark:border-slate-700 pt-2">
                            <span class="font-bold text-pink-600 dark:text-pink-400 block mb-1 text-base">📝 بیوگرافی:</span>
                            <p class="italic leading-relaxed text-gray-700 dark:text-slate-400">{{ $user->bio ?? 'توضیحاتی ثبت نشده است.' }}</p>
                        </div>
                    </div>

                    <div class="mt-8 flex flex-wrap justify-center gap-3">
                       <button onclick="window.history.back()" 
                            class="flex-1 min-w-[120px] text-center bg-gray-100 hover:bg-gray-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-gray-600 dark:text-slate-300 font-bold py-3 px-6 rounded-2xl transition duration-300">
                        بازگشت
                        </button>

                        <a href="#" 
                           class="flex-1 min-w-[120px] text-center bg-gradient-to-r from-pink-500 to-rose-500 hover:from-pink-600 hover:to-rose-600 text-white font-bold py-3 px-6 rounded-2xl shadow-lg shadow-pink-200 dark:shadow-none transition duration-300 transform hover:-translate-y-1">
                            مشاهده کامل
                        </a>
                    </div>

                    @if (auth()->check() && auth()->id() !== $user->id)
                        <div class="w-full flex flex-col sm:flex-row gap-3 mt-4 border-t border-gray-100 dark:border-slate-800 pt-4">
                            
                            <form action="{{ route('report.store') }}" method="POST" class="flex-1">
                                @csrf
                                <input type="hidden" name="reported_id" value="{{ $user->id }}">
                                <input type="hidden" name="reason" value="گزارش سریع / بررسی پروفایل">
                                
                                <button type="submit" 
                                        class="w-full bg-white dark:bg-slate-900 border-2 border-red-100 dark:border-red-950 hover:border-red-500 dark:hover:border-red-500 text-red-500 dark:text-red-400 font-bold py-3 px-4 rounded-2xl transition duration-300 flex items-center justify-center gap-2 shadow-sm">
                                    <span>🚫</span> گزارش سریع
                                </button>
                            </form>

                            <button onclick="document.getElementById('reportModal').classList.remove('hidden')" 
                                    class="flex-1 bg-amber-50 dark:bg-amber-950/30 hover:bg-amber-100 dark:hover:bg-amber-950/60 text-amber-600 dark:text-amber-400 font-bold py-3 px-4 rounded-2xl transition duration-300 flex items-center justify-center gap-2 border-2 border-transparent hover:border-amber-200 dark:hover:border-amber-900 shadow-sm">
                                <span>⚠️</span> گزارش با جزئیات
                            </button>

                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<div id="reportModal" class="fixed inset-0 z-50 flex items-center justify-center hidden p-4 transition-all">
    <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm" onclick="document.getElementById('reportModal').classList.add('hidden')"></div>
    <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-2xl z-50 w-full max-w-md overflow-hidden transform transition-all border border-transparent dark:border-slate-800">
        <div class="bg-rose-50 dark:bg-rose-950/30 px-6 py-4 border-b border-rose-100 dark:border-rose-900/50 flex justify-between items-center">
            <h3 class="text-xl font-bold text-rose-700 dark:text-rose-400">گزارش تخلف کاربر</h3>
            <button type="button" class="text-gray-400 hover:text-gray-600 dark:text-slate-400 dark:hover:text-slate-200 text-2xl"
                    onclick="document.getElementById('reportModal').classList.add('hidden')">
                &times;
            </button>
        </div>
        <div class="p-6">
            <form action="{{ route('report.store') }}" method="POST">
                @csrf
                <input type="hidden" name="reported_id" value="{{ $user->id }}">
                
                <label for="reason_select" class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-2">علت اصلی گزارش:</label>
                <select name="reason_category" id="reason_select" class="w-full bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-slate-200 rounded-xl p-3 mb-4 outline-none focus:ring-2 focus:ring-pink-400">
                    <option value="مزاحمت">مزاحمت در چت</option>
                    <option value="محتوای نامناسب">پروفایل یا عکس نامناسب</option>
                    <option value="اسپم">کاربر فیک / اسپم</option>
                    <option value="سایر">سایر موارد</option>
                </select>

                <label for="reason" class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-2">توضیحات بیشتر (اختیاری):</label>
                <textarea name="reason" id="reason" rows="4" 
                          class="w-full bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-slate-200 rounded-2xl p-4 focus:ring-2 focus:ring-pink-400 focus:border-transparent outline-none transition" 
                          placeholder="لطفاً دلیل خود را بنویسید تا ادمین بررسی کند..." required></textarea>
                
                <button type="submit" 
                        class="w-full mt-6 bg-gradient-to-r from-red-500 to-rose-600 text-white font-bold py-3 rounded-2xl shadow-lg hover:shadow-red-200 dark:shadow-none transition duration-300">
                    ثبت و ارسال گزارش
                </button>
            </form>
        </div>
    </div>
</div>

<script>
function ajaxLike(event) {
    event.preventDefault(); // جلوگیری از رفرش شدن صفحه

    const form = document.getElementById('like-form');
    const btn = document.getElementById('like-btn');
    const icon = document.getElementById('like-icon');
    const url = form.action;
    const token = form.querySelector('input[name="_token"]').value;

    // ارسال درخواست به سرور در پس‌زمینه
    fetch(url, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': token,
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        if (response.ok) {
            // تغییر آنی ظاهر دکمه بدون رفرش صفحه
            if (icon.classList.contains('far')) {
                // تبدیل به حالت لایک شده (قرمز)
                icon.className = 'fa fa-heart text-xl';
                btn.className = 'p-3 bg-red-500 text-white rounded-2xl shadow-lg shadow-red-200 dark:shadow-none hover:bg-red-600 transition duration-300 transform hover:-translate-y-0.5 flex items-center justify-center w-12 h-12';
                btn.title = 'برداشتن لایک';
            } else {
                // تبدیل به حالت لایک نشده (خالی)
                icon.className = 'far fa-heart text-xl';
                btn.className = 'p-3 bg-pink-50 dark:bg-slate-800 text-pink-600 dark:text-pink-400 border border-pink-200 dark:border-slate-700 rounded-2xl hover:bg-pink-600 hover:text-white dark:hover:bg-pink-600 dark:hover:text-white transition duration-300 transform hover:-translate-y-0.5 flex items-center justify-center w-12 h-12';
                btn.title = 'لایک کردن';
            }
        }
    })
    .catch(error => console.error('خطا در ارسال لایک:', error));
}
</script>

@endsection