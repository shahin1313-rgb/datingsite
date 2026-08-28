<main class="main-content w-full p-4 md:p-8 space-y-8">
    
    <header class="flex flex-col md:flex-row md:items-center justify-between gap-4 animate-slide-in">
        <div>
            <h2 class="text-3xl font-black text-gray-800 tracking-tight">پیشخوان من</h2>
            <p class="text-gray-500 mt-1">خلاصه وضعیت فعالیت‌های شما در یک نگاه</p>
        </div>
        <div class="flex items-center gap-2 bg-white p-2 rounded-2xl shadow-sm border border-gray-100">
            <span class="w-3 h-3 {{ auth()->user()->isOnline() ? 'bg-green-500 animate-pulse' : 'bg-gray-400' }} rounded-full mr-2"></span>
            <span class="text-sm font-bold text-gray-700">
                وضعیت شما: {{ auth()->user()->isOnline() ? 'آنلاین' : 'آفلاین' }}
            </span>
        </div>
    </header>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">
        <a href="{{ route('messages.index') }}" class="group bg-white p-5 rounded-[2rem] border border-gray-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
            <div class="w-12 h-12 bg-pink-100 text-pink-600 rounded-2xl flex items-center justify-center mb-4 group-hover:bg-pink-600 group-hover:text-white transition-colors">
                <i class="fa fa-comment-dots text-xl"></i>
            </div>
            <span class="block text-2xl font-black text-gray-800">{{ auth()->user()->unreadMessagesCount() }}</span>
            <span class="text-sm text-gray-500 font-medium">پیام جدید</span>
        </a>

        <div class="bg-white p-5 rounded-[2rem] border border-gray-100 shadow-sm hover:shadow-xl transition-all duration-300">
            <div class="w-12 h-12 bg-red-100 text-red-600 rounded-2xl flex items-center justify-center mb-4">
                <i class="fa fa-heart text-xl"></i>
            </div>
            <span class="block text-2xl font-black text-gray-800">{{ $likesCount }}</span>
            <span class="text-sm text-gray-500 font-medium">لایک دریافتی</span>
        </div>

        <div class="bg-white p-5 rounded-[2rem] border border-gray-100 shadow-sm hover:shadow-xl transition-all duration-300">
            <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-2xl flex items-center justify-center mb-4">
                <i class="fa fa-wallet text-xl"></i>
            </div>
            <span class="block text-2xl font-black text-gray-800">۲۳</span>
            <span class="text-sm text-gray-500 font-medium">اعتبار (روز)</span>
        </div>

        <div class="bg-white p-5 rounded-[2rem] border border-gray-100 shadow-sm hover:shadow-xl transition-all duration-300">
            <div class="w-12 h-12 bg-amber-100 text-amber-600 rounded-2xl flex items-center justify-center mb-4">
                <i class="fa fa-eye text-xl"></i>
            </div>
            <span class="block text-2xl font-black text-gray-800">{{ $totalViews }}</span>
            <span class="text-sm text-gray-500 font-medium">کل بازدیدها</span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <div class="lg:col-span-2 bg-white rounded-[2.5rem] shadow-sm border border-gray-100 p-6 md:p-8">
            <div class="flex items-center justify-between mb-8">
                <h3 class="text-xl font-black text-gray-800">❤️ لایک‌های اخیر</h3>
                <a href="{{ route('likes.received') }}" class="text-sm font-bold text-pink-600 hover:underline">مشاهده همه</a>
            </div>

            <div class="flex overflow-x-auto gap-6 pb-4 no-scrollbar">
                @foreach($latestLikers as $like)
                <div class="flex-shrink-0 flex flex-col items-center group">
                    <div class="relative">
                        <img src="{{ $like->liker->profilePhotoUrl() }}"
                             class="w-20 h-20 rounded-[2rem] object-cover ring-4 ring-pink-50 group-hover:ring-pink-200 transition-all">
                        <span class="absolute -bottom-1 -right-1 bg-red-500 w-5 h-5 rounded-full border-2 border-white flex items-center justify-center">
                            <i class="fa fa-heart text-[8px] text-white"></i>
                        </span>
                    </div>
                    <span class="mt-3 text-xs font-bold text-gray-700">{{ $like->liker->name }}</span>
                </div>
                @endforeach
            </div>
        </div>

        <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 p-6">
            <h3 class="text-xl font-black text-gray-800 mb-6 italic">بازدیدکنندگان</h3>
            <div class="space-y-5">
                @forelse($recentProfileViews as $view)
                <div class="flex items-center gap-4 group">
                    <img src="{{ $view->viewer->profilePhotoUrl() }}"
                         class="w-12 h-12 rounded-2xl object-cover">
                    <div class="flex-1">
                        <h4 class="text-sm font-bold text-gray-800 group-hover:text-pink-600 transition">{{ $view->viewer->name }}</h4>
                        <p class="text-[10px] text-gray-400">{{ $view->created_at->diffForHumans() }}</p>
                    </div>
                    <i class="fa fa-chevron-left text-gray-300 text-xs"></i>
                </div>
                @empty
                <p class="text-sm text-gray-500 text-center py-4">هنوز بازدیدی ندارید</p>
                @endforelse
            </div>
        </div>
    </div>

    <section>
        <h3 class="text-xl font-black text-gray-800 mb-6 flex items-center gap-2">
            <span class="w-2 h-8 bg-orange-500 rounded-full"></span>
            اعضای فعال و آنلاین
        </h3>
        <div class="bg-gradient-to-br from-gray-900 to-gray-800 rounded-[2.5rem] p-8 overflow-hidden relative">
            <div class="flex overflow-x-auto gap-8 no-scrollbar relative z-10">
                @foreach ($recentUsers as $recentUser)
                <a href="{{ route('profile.show', $recentUser->id) }}" class="flex-shrink-0 text-center group">
                    <div class="relative inline-block">
                        <img src="{{ $recentUser->profilePhotoUrl() }}"
                             class="w-16 h-16 rounded-2xl object-cover grayscale group-hover:grayscale-0 transition-all duration-500 scale-90 group-hover:scale-110 shadow-2xl">
                        @if ($recentUser->isOnline())
                            <span class="absolute top-0 right-0 w-3 h-3 bg-green-500 rounded-full border-2 border-gray-900"></span>
                        @endif
                    </div>
                    <p class="mt-3 text-xs font-medium text-gray-400 group-hover:text-white transition">{{ $recentUser->name }}</p>
                </a>
                @endforeach
            </div>
            <div class="absolute -top-10 -left-10 w-40 h-40 bg-white/5 rounded-full blur-3xl"></div>
        </div>
    </section>

    <footer class="py-10 text-center border-t border-gray-100 mt-12">
        <p class="text-gray-400 text-sm italic">مدیریت روابط شما به سبک مدرن</p>
    </footer>

</main>

<style>
    /* مخفی کردن اسکرول‌بار برای زیبایی بیشتر */
    .no-scrollbar::-webkit-scrollbar { display: none; }
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>
