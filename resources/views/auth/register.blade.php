@extends('layouts.app')

@section('content')
<div class="fixed inset-0 sm:relative sm:min-h-screen bg-pink-50/30 dark:bg-slate-950 flex items-center justify-center transition-colors duration-300 px-0 sm:px-4 sm:py-8"
     x-data="{ step: 1, maxStep: 3 }">
    
    <div class="w-full h-full sm:h-auto sm:max-w-xl px-6 py-5 bg-white dark:bg-slate-900 sm:rounded-3xl sm:shadow-2xl border-0 sm:border border-pink-100/50 dark:border-slate-800 flex flex-col justify-between transition-all duration-300 overflow-hidden">
        
        <div class="select-none flex-shrink-0 pt-2 sm:pt-0">
            <h2 class="text-xl font-black text-center text-gray-800 dark:text-slate-100 mb-3">
                {{ __('Registerpage.register') }}
            </h2>
            
            <div class="relative w-full h-1.5 bg-gray-100 dark:bg-slate-800 rounded-full overflow-hidden mb-2">
                <div class="absolute top-0 right-0 h-full bg-gradient-to-l from-pink-500 to-rose-500 transition-all duration-500 ease-out"
                     :style="'width: ' + ((step / maxStep) * 100) + '%'"></div>
            </div>
            
            <div class="flex justify-between items-center text-[10px] font-bold text-gray-400 dark:text-slate-500 mb-3">
                <span :class="step >= 1 ? 'text-pink-500 dark:text-pink-400' : ''">۱. ایجاد حساب</span>
                <span :class="step >= 2 ? 'text-pink-500 dark:text-pink-400' : ''">۲. مشخصات فردی</span>
                <span :class="step >= 3 ? 'text-pink-500 dark:text-pink-400' : ''">۳. جذابیت‌ها</span>
            </div>
        </div>

        <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data" class="flex-1 flex flex-col justify-between overflow-hidden">
            @csrf

            <div class="flex-1 overflow-y-auto px-1 py-2 space-y-4 min-h-0">
                
                <div x-show="step === 1" x-transition:enter="transition ease-out duration-200" class="space-y-4">
                    <div>
                        <label for="name" class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-1.5">{{ __('Registerpage.name') }}</label>
                        <input type="text" id="name" name="name"
                               class="block w-full rounded-2xl border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-slate-800/50 text-gray-800 dark:text-slate-200 p-3 focus:border-pink-500 focus:ring focus:ring-pink-200 dark:focus:ring-pink-900/30 outline-none transition-all"
                               value="{{ old('name') }}" required placeholder="نام شما برای نمایش در پروفایل">
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-1.5">{{ __('Registerpage.email') }}</label>
                        <input type="email" id="email" name="email"
                               class="block w-full rounded-2xl border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-slate-800/50 text-gray-800 dark:text-slate-200 p-3 focus:border-pink-500 focus:ring focus:ring-pink-200 dark:focus:ring-pink-900/30 outline-none transition-all"
                               value="{{ old('email') }}" required placeholder="name@example.com">
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-1.5">{{ __('Registerpage.password') }}</label>
                        <input type="password" id="password" name="password"
                               class="block w-full rounded-2xl border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-slate-800/50 text-gray-800 dark:text-slate-200 p-3 focus:border-pink-500 focus:ring focus:ring-pink-200 dark:focus:ring-pink-900/30 outline-none transition-all" 
                               required placeholder="••••••••">
                    </div>

                    <div>
                        <label for="password-confirm" class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-1.5">{{ __('Registerpage.password_confirm') }}</label>
                        <input type="password" id="password-confirm" name="password_confirmation"
                               class="block w-full rounded-2xl border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-slate-800/50 text-gray-800 dark:text-slate-200 p-3 focus:border-pink-500 focus:ring focus:ring-pink-200 dark:focus:ring-pink-900/30 outline-none transition-all" 
                               required placeholder="••••••••">
                    </div>
                </div>

                <div x-show="step === 2" x-transition:enter="transition ease-out duration-200" x-cloak class="space-y-4">
                    <div>
                        <label for="gender" class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-1.5">{{ __('Registerpage.gender') }}</label>
                        <select id="gender" name="gender" class="block w-full rounded-2xl border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-slate-800/50 text-gray-800 dark:text-slate-200 p-3 focus:border-pink-500 focus:ring focus:ring-pink-200 dark:focus:ring-pink-900/30 outline-none transition-all">
                            <option value="male">{{ __('Registerpage.male') }}</option>
                            <option value="female">{{ __('Registerpage.female') }}</option>
                        </select>
                    </div>

                    <div>
                        <label for="age" class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-1.5">{{ __('Registerpage.age') }}</label>
                        <input type="number" id="age" name="age" min="18" max="100"
                               class="block w-full rounded-2xl border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-slate-800/50 text-gray-800 dark:text-slate-200 p-3 focus:border-pink-500 focus:ring focus:ring-pink-200 dark:focus:ring-pink-900/30 outline-none transition-all"
                               value="{{ old('age') }}" required placeholder="سن شما (حداقل ۱۸ سال)">
                    </div>

                    <div>
                        <label for="city" class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-1.5">{{ __('Registerpage.city') }}</label>
                        <input type="text" id="city" name="city"
                               class="block w-full rounded-2xl border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-slate-800/50 text-gray-800 dark:text-slate-200 p-3 focus:border-pink-500 focus:ring focus:ring-pink-200 dark:focus:ring-pink-900/30 outline-none transition-all"
                               value="{{ old('city') }}" required placeholder="مثال: تهران">
                    </div>

                    <div>
                        <label for="marital_status" class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-1.5">{{ __('Registerpage.marital_status') }}</label>
                        <select id="marital_status" name="marital_status" required
                                class="block w-full rounded-2xl border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-slate-800/50 text-gray-800 dark:text-slate-200 p-3 focus:border-pink-500 focus:ring focus:ring-pink-200 dark:focus:ring-pink-900/30 outline-none transition-all">
                            <option value="">{{ __('Registerpage.select') }}</option>
                            <option value="single">{{ __('Registerpage.single') }}</option>
                            <option value="married">{{ __('Registerpage.married') }}</option>
                            <option value="divorced">{{ __('Registerpage.divorced') }}</option>
                            <option value="widowed">{{ __('Registerpage.widowed') }}</option>
                        </select>
                    </div>
                </div>

                <div x-show="step === 3" x-transition:enter="transition ease-out duration-200" x-cloak class="space-y-4">
                    <div>
                        <label for="interested_in" class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-1.5">{{ __('Registerpage.interested_in') }}</label>
                        <select id="interested_in" name="interested_in"
                                class="block w-full rounded-2xl border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-slate-800/50 text-gray-800 dark:text-slate-200 p-3 focus:border-pink-500 focus:ring focus:ring-pink-200 dark:focus:ring-pink-900/30 outline-none transition-all">
                            <option value="sport">ورزش / Sport</option>
                            <option value="travel">مسافرت / Voyage</option>
                            <option value="books">کتاب / Livre</option>
                            <option value="party">مهمانی / Fête</option>
                        </select>
                    </div>

                    <div>
                        <label for="salary" class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-1.5">{{ __('Registerpage.salary') }} (میلیون تومان)</label>
                        <input type="number" id="salary" name="salary" min="0"
                               class="block w-full rounded-2xl border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-slate-800/50 text-gray-800 dark:text-slate-200 p-3 focus:border-pink-500 focus:ring focus:ring-pink-200 dark:focus:ring-pink-900/30 outline-none transition-all"
                               value="{{ old('salary') }}" placeholder="میزان درآمد تقریبی ماهانه">
                    </div>

                    <div>
                        <label for="bio" class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-1.5">{{ __('Registerpage.bio') }}</label>
                        <textarea id="bio" name="bio" rows="2"
                                  class="block w-full rounded-2xl border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-slate-800/50 text-gray-800 dark:text-slate-200 p-3 focus:border-pink-500 focus:ring focus:ring-pink-200 dark:focus:ring-pink-900/30 outline-none transition-all resize-none"
                                  placeholder="کمی از خودتان بنویسید..."></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-1.5">{{ __('Registerpage.profile_picture') }}</label>
                        <div class="flex items-center justify-center w-full">
                            <label class="flex flex-col items-center justify-center w-full h-20 border-2 border-gray-200 dark:border-slate-700 border-dashed rounded-2xl cursor-pointer bg-gray-50 dark:bg-slate-800/30 hover:bg-gray-100 transition duration-200">
                                <div class="flex flex-col items-center justify-center pt-2 pb-2">
                                    <span class="text-xl mb-0.5">📸</span>
                                    <p class="text-[11px] text-gray-500 dark:text-slate-400 font-semibold">انتخاب عکس پروفایل</p>
                                </div>
                                <input type="file" id="profile_picture" name="profile_picture" accept="image/*" class="hidden">
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-3 pt-3 mt-2 border-t border-gray-100 dark:border-slate-800/80 flex-shrink-0 pb-2 sm:pb-0">
                <button type="button" 
                        x-show="step > 1" 
                        @click="step--"
                        class="flex-1 py-3 text-center bg-gray-100 dark:bg-slate-800 hover:bg-gray-200 dark:hover:bg-slate-700 text-gray-600 dark:text-slate-300 font-bold rounded-2xl transition duration-300 select-none outline-none text-sm">
                    مرحله قبل
                </button>

                <button type="button" 
                        x-show="step < maxStep" 
                        @click="step++"
                        class="flex-1 py-3 text-center bg-gradient-to-r from-pink-500 to-rose-500 hover:from-pink-600 hover:to-rose-600 text-white font-bold rounded-2xl shadow-lg shadow-pink-100 dark:shadow-none hover:shadow-none transition duration-300 select-none outline-none text-sm">
                    مرحله بعد
                </button>

                <button type="submit" 
                        x-show="step === maxStep"
                        class="flex-1 py-3 text-center bg-gradient-to-r from-emerald-500 to-green-500 hover:from-emerald-600 hover:to-green-600 text-white font-bold rounded-2xl shadow-lg shadow-green-100 dark:shadow-none hover:shadow-none transition duration-300 select-none outline-none text-sm">
                    {{ __('Registerpage.submit') }}
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
    
    /* بهینه‌سازی اکسپرینس اسکرول باکس داخلی روی آیفون و اندروید */
    .overflow-y-auto {
        -webkit-overflow-scrolling: touch;
    }
    .overflow-y-auto::-webkit-scrollbar {
        width: 3px;
    }
    .overflow-y-auto::-webkit-scrollbar-thumb {
        background-color: rgba(244, 63, 94, 0.2);
        border-radius: 10px;
    }
</style>
@endsection