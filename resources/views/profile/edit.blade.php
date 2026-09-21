@extends('layouts.app')

@section('title', 'ویرایش پروفایل | ولورا')

@section('content')
<div class="vlora-shell py-6 sm:py-10" dir="{{ app()->isLocale('fa') ? 'rtl' : 'ltr' }}" x-data="{ photoName: '', showNew: false, showConfirm: false, showCurrent: false, bioLength: {{ mb_strlen((string) old('bio', $user->bio)) }} }">
    <div class="mx-auto max-w-4xl">
        <header class="mb-6 flex flex-col gap-4 sm:mb-8 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="mb-3 flex items-center gap-2 text-xs font-bold text-rose-400"><i class="fas fa-user-pen" aria-hidden="true"></i> حساب کاربری</p>
                <h1 class="text-2xl font-black tracking-tight text-white sm:text-3xl">ویرایش پروفایل</h1>
                <p class="mt-2 text-sm leading-7 text-zinc-400">اطلاعات دقیق‌تر کمک می‌کند افراد مناسب‌تری شما را پیدا کنند.</p>
            </div>
            <a href="{{ route('dashboard') }}" class="vlora-btn-secondary self-start text-sm sm:self-auto"><i class="fas fa-eye" aria-hidden="true"></i> مشاهده پروفایل</a>
        </header>

        @if ($errors->any())
            <div class="mb-6 rounded-2xl border border-red-500/25 bg-red-500/10 p-4" role="alert" aria-labelledby="profile-errors-title">
                <div class="flex gap-3">
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-red-500/15 text-red-400"><i class="fas fa-circle-exclamation" aria-hidden="true"></i></span>
                    <div><h2 id="profile-errors-title" class="font-black text-red-200">لطفاً موارد زیر را اصلاح کنید</h2><ul class="mt-2 list-inside list-disc space-y-1 text-sm leading-6 text-red-300">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
                </div>
            </div>
        @endif

        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf

            <section class="vlora-panel overflow-hidden" aria-labelledby="photo-heading">
                <div class="relative h-32 overflow-hidden bg-gradient-to-l from-rose-700 via-fuchsia-800 to-zinc-900 sm:h-40">
                    <div class="absolute inset-0 opacity-20" style="background-image:radial-gradient(circle at 20% 20%,white 0 1px,transparent 1px);background-size:24px 24px"></div>
                    <div class="absolute inset-0 bg-gradient-to-t from-zinc-950/60 to-transparent"></div>
                </div>
                <div class="px-4 pb-5 sm:px-7 sm:pb-7">
                    <div class="relative -mt-12 flex flex-col gap-4 sm:-mt-14 sm:flex-row sm:items-end sm:justify-between">
                        <div class="flex items-end gap-4">
                            <div class="relative shrink-0">
                                <img id="header-avatar" src="{{ $user->profilePhotoUrl() }}?v={{ time() }}" class="h-24 w-24 rounded-3xl border-4 border-zinc-900 object-cover shadow-2xl sm:h-28 sm:w-28" alt="تصویر پروفایل {{ $user->name }}">
                                <span class="absolute -bottom-1 -left-1 flex h-9 w-9 items-center justify-center rounded-full border-4 border-zinc-900 bg-rose-500 text-white"><i class="fas fa-camera text-xs" aria-hidden="true"></i></span>
                            </div>
                            <div class="min-w-0 pb-1"><h2 id="photo-heading" class="truncate text-xl font-black text-white">{{ $user->name }}</h2><p class="mt-1 flex items-center gap-2 text-xs font-bold text-zinc-400"><span class="h-2 w-2 rounded-full bg-emerald-400"></span> پروفایل فعال</p></div>
                        </div>
                        <label for="profile_picture" class="inline-flex min-h-11 cursor-pointer items-center justify-center gap-2 rounded-full border border-white/10 bg-white/[0.06] px-4 text-sm font-extrabold text-white transition hover:border-rose-400/40 hover:bg-rose-500/10"><i class="fas fa-arrow-up-from-bracket text-rose-400" aria-hidden="true"></i><span x-text="photoName || 'تغییر تصویر'">تغییر تصویر</span></label>
                        <input type="file" id="profile_picture" name="profile_picture" accept=".jpeg,.jpg,.png,.gif,image/jpeg,image/png,image/gif" class="sr-only" @change="photoName = $event.target.files[0]?.name || ''">
                    </div>
                    <p class="mt-4 text-xs leading-6 text-zinc-500 sm:mr-32">JPG، PNG یا GIF؛ حداکثر ۲ مگابایت و ۶ مگاپیکسل</p>
                    @error('profile_picture')<p class="mt-2 text-xs font-bold text-rose-400">{{ $message }}</p>@enderror
                </div>
            </section>

            <section class="vlora-panel p-4 sm:p-7" aria-labelledby="basic-info-heading">
                <div class="mb-6 flex items-center gap-3 border-b border-white/10 pb-4">
                    <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-rose-500/10 text-rose-400"><i class="fas fa-id-card" aria-hidden="true"></i></span>
                    <div><h2 id="basic-info-heading" class="font-black text-white">اطلاعات اصلی</h2><p class="mt-1 text-xs text-zinc-500">اطلاعاتی که در پروفایل شما نمایش داده می‌شود</p></div>
                </div>
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div><label for="name" class="mb-2 block text-sm font-bold text-zinc-300">{{ __('profile.name') }} <span class="text-rose-400">*</span></label><input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" class="vlora-field" autocomplete="name" required></div>
                    <div><label for="email" class="mb-2 block text-sm font-bold text-zinc-300">{{ __('profile.email') }} <span class="text-rose-400">*</span></label><input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" class="vlora-field text-left" dir="ltr" autocomplete="email" required>@error('email')<p class="mt-2 text-xs font-bold text-rose-400">{{ $message }}</p>@enderror</div>
                    <div><label for="age" class="mb-2 block text-sm font-bold text-zinc-300">سن <span class="text-rose-400">*</span></label><input id="age" name="age" type="number" min="18" max="100" required value="{{ old('age', $user->age) }}" class="vlora-field"></div>
                    <div><label for="gender" class="mb-2 block text-sm font-bold text-zinc-300">جنسیت <span class="text-rose-400">*</span></label><select id="gender" name="gender" required class="vlora-field"><option value="male" @selected(old('gender', $user->gender)==='male')>مرد</option><option value="female" @selected(old('gender', $user->gender)==='female')>زن</option><option value="other" @selected(old('gender', $user->gender)==='other')>سایر</option></select></div>
                    <div><label for="interested_in" class="mb-2 block text-sm font-bold text-zinc-300">علاقه‌مند به <span class="text-rose-400">*</span></label><input id="interested_in" name="interested_in" required maxlength="100" value="{{ old('interested_in', $user->interested_in) }}" class="vlora-field"></div>
                    <div><label for="city" class="mb-2 block text-sm font-bold text-zinc-300">{{ __('profile.city') }}</label><input type="text" id="city" name="city" value="{{ old('city', $user->city) }}" class="vlora-field" autocomplete="address-level2" placeholder="مثلاً تهران"></div>
                    <div><label for="marital_status" class="mb-2 block text-sm font-bold text-zinc-300">{{ __('profile.marital_status') }}</label><select id="marital_status" name="marital_status" class="vlora-field"><option value="">{{ __('profile.select') }}</option><option value="single" @selected(old('marital_status', $user->marital_status)==='single')>{{ __('profile.single') }}</option><option value="married" @selected(old('marital_status', $user->marital_status)==='married')>{{ __('profile.married') }}</option><option value="divorced" @selected(old('marital_status', $user->marital_status)==='divorced')>{{ __('profile.divorced') }}</option><option value="widowed" @selected(old('marital_status', $user->marital_status)==='widowed')>{{ __('profile.widowed') }}</option></select></div>
                    <div><label for="salary" class="mb-2 block text-sm font-bold text-zinc-300">درآمد ماهانه <span class="font-normal text-zinc-500">(اختیاری)</span></label><div class="relative"><input id="salary" name="salary" type="number" min="0" value="{{ old('salary', $user->salary) }}" class="vlora-field pl-16" placeholder="۰"><span class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-xs text-zinc-500">تومان</span></div></div>
                    <div class="sm:col-span-2"><label for="bio" class="mb-2 flex items-center justify-between gap-3 text-sm font-bold text-zinc-300"><span>{{ __('profile.bio') }}</span><span class="text-xs font-normal text-zinc-500"><span x-text="bioLength">0</span>/۱۰۰۰</span></label><textarea id="bio" name="bio" rows="5" maxlength="1000" placeholder="از علایق، سبک زندگی و چیزهایی که برایتان مهم است بنویسید..." class="vlora-field resize-y leading-7" @input="bioLength = $event.target.value.length">{{ old('bio', $user->bio) }}</textarea></div>
                </div>
                <label class="mt-5 flex cursor-pointer items-start gap-3 rounded-2xl border border-white/10 bg-white/[0.035] p-4 transition hover:border-rose-400/30"><input type="hidden" name="salary_visible" value="0"><input type="checkbox" name="salary_visible" value="1" @checked(old('salary_visible', $user->salary_visible)) class="mt-1 h-5 w-5 rounded border-zinc-600 bg-zinc-800 text-rose-500 focus:ring-rose-500 focus:ring-offset-zinc-900"><span><strong class="block text-sm text-zinc-200">نمایش عمومی درآمد</strong><span class="mt-1 block text-xs leading-6 text-zinc-500">درآمد فقط با انتخاب صریح شما در پروفایل عمومی نمایش داده می‌شود.</span></span></label>
            </section>

            <section class="vlora-panel p-4 sm:p-7" aria-labelledby="security-heading">
                <div class="mb-6 flex items-center gap-3 border-b border-white/10 pb-4"><span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-amber-400/10 text-amber-300"><i class="fas fa-shield-halved" aria-hidden="true"></i></span><div><h2 id="security-heading" class="font-black text-white">امنیت حساب</h2><p class="mt-1 text-xs text-zinc-500">اگر قصد تغییر رمز ندارید، این قسمت را خالی بگذارید</p></div></div>
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div><label for="new_password" class="mb-2 block text-sm font-bold text-zinc-300">رمز عبور جدید</label><div class="relative"><input id="new_password" name="new_password" :type="showNew ? 'text' : 'password'" autocomplete="new-password" class="vlora-field pl-12"><button type="button" @click="showNew = !showNew" class="absolute inset-y-0 left-1 flex w-11 items-center justify-center text-zinc-500 hover:text-white" :aria-label="showNew ? 'پنهان کردن رمز' : 'نمایش رمز'"><i class="fas" :class="showNew ? 'fa-eye-slash' : 'fa-eye'" aria-hidden="true"></i></button></div></div>
                    <div><label for="new_password_confirmation" class="mb-2 block text-sm font-bold text-zinc-300">تکرار رمز عبور جدید</label><div class="relative"><input id="new_password_confirmation" name="new_password_confirmation" :type="showConfirm ? 'text' : 'password'" autocomplete="new-password" class="vlora-field pl-12"><button type="button" @click="showConfirm = !showConfirm" class="absolute inset-y-0 left-1 flex w-11 items-center justify-center text-zinc-500 hover:text-white" :aria-label="showConfirm ? 'پنهان کردن رمز' : 'نمایش رمز'"><i class="fas" :class="showConfirm ? 'fa-eye-slash' : 'fa-eye'" aria-hidden="true"></i></button></div></div>
                    <div class="sm:col-span-2"><label for="current_password" class="mb-2 block text-sm font-bold text-zinc-300">{{ __('profile.current_password') }}</label><div class="relative"><input id="current_password" name="current_password" :type="showCurrent ? 'text' : 'password'" autocomplete="current-password" aria-describedby="current-password-help" class="vlora-field pl-12"><button type="button" @click="showCurrent = !showCurrent" class="absolute inset-y-0 left-1 flex w-11 items-center justify-center text-zinc-500 hover:text-white" :aria-label="showCurrent ? 'پنهان کردن رمز' : 'نمایش رمز'"><i class="fas" :class="showCurrent ? 'fa-eye-slash' : 'fa-eye'" aria-hidden="true"></i></button></div><p id="current-password-help" class="mt-2 text-xs leading-6 text-zinc-500">{{ __('profile.password_required_for_email_change') }}</p>@error('current_password')<p class="mt-2 text-xs font-bold text-rose-400">{{ $message }}</p>@enderror</div>
                </div>
            </section>

            <div class="sticky bottom-[4.5rem] z-30 rounded-3xl border border-white/10 bg-zinc-950/90 p-3 shadow-2xl backdrop-blur-xl sm:bottom-4 sm:flex sm:items-center sm:justify-between sm:px-5">
                <p class="hidden text-xs text-zinc-500 sm:block">با ذخیره، تغییرات پروفایل شما بلافاصله اعمال می‌شود.</p>
                <div class="flex gap-2"><a href="{{ route('dashboard') }}" class="vlora-btn-secondary flex-1 text-sm sm:flex-none">انصراف</a><button type="submit" class="vlora-btn-primary flex-[2] text-sm sm:flex-none sm:px-8"><i class="fas fa-check" aria-hidden="true"></i> {{ __('profile.save') }}</button></div>
            </div>
        </form>
    </div>
</div>

<script nonce="{{ \Illuminate\Support\Facades\Vite::cspNonce() }}">
    document.getElementById('profile_picture')?.addEventListener('change', (event) => {
        const file = event.target.files?.[0];
        if (!file || !file.type.startsWith('image/')) return;
        const reader = new FileReader();
        reader.addEventListener('load', () => {
            const avatar = document.getElementById('header-avatar');
            if (avatar) avatar.src = reader.result;
        });
        reader.readAsDataURL(file);
    });
</script>
@endsection
