 <main class="main-content md:mr-64 md:ml-0 p-6 w-full">
     <button class="md:hidden text-gray-800 p-2 focus:outline-none" onclick="toggleSidebar()">
         <i class="fa fa-bars text-2xl"></i>
     </button>
     <header class="mb-8 animate-slide-in">
         <h2 class="text-3xl font-bold text-gray-800"><i class="fa fa-dashboard ml-2"></i> داشبورد من</h2>
     </header>

     <!-- باکس‌های اطلاعات -->
     <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
         <a href="{{ route('messages.index') }}"
             class="card bg-gradient-to-br from-red-500 to-pink-500 text-white p-6 rounded-xl animate-slide-in"
             style="animation-delay: 0.1s;">
             <div class="flex items-center justify-between">
                 <i class="fa fa-comment text-4xl"></i>
                 <h3 class="text-2xl font-bold">{{ auth()->user()->unreadMessagesCount() }}</h3>
             </div>
             <h4 class="mt-2 text-lg">پیام جدید</h4>
         </a>
         <a href="{{ route('profile.edit') }}"
             class="card bg-gradient-to-br from-blue-500 to-indigo-500 text-white p-6 rounded-xl animate-slide-in"
             style="animation-delay: 0.2s;">
             <div class="flex items-center justify-between">
                 <i class="fa fa-user text-4xl"></i>
                 <h3 class="text-2xl font-bold">ویرایش</h3>
             </div>
             <h4 class="mt-2 text-lg">پروفایل</h4>
         </a>
         <div class="card bg-gradient-to-br from-teal-500 to-cyan-500 text-white p-6 rounded-xl animate-slide-in"
             style="animation-delay: 0.3s;">
             <div class="flex items-center justify-between">
                 <i class="fa fa-credit-card text-4xl"></i>
                 <h3 class="text-2xl font-bold">23</h3>
             </div>
             <h4 class="mt-2 text-lg">پرداخت شارژ</h4>
         </div>
         <div class="card bg-gradient-to-br from-orange-500 to-yellow-500 text-white p-6 rounded-xl animate-slide-in"
             style="animation-delay: 0.4s;">
             <div class="flex items-center justify-between">
                 <i class="fa fa-heart text-4xl"></i>
                 <h3 class="text-2xl font-bold">50</h3>
             </div>
             <h4 class="mt-2 text-lg">افراد مچ‌شده</h4>
         </div>
     </div>

     <!-- فعالیت‌ها و نوتیفیکیشن‌ها -->
     <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-12">
         <div class="lg:col-span-2 animate-slide-in" style="animation-delay: 0.5s;">
            <div class="bg-white rounded-xl shadow-md p-6">
    <h3 class="text-xl font-bold text-red-600 mb-4">
        ❤️ لایک‌های دریافتی شما
    </h3>

    <div class="text-3xl font-bold mb-2">{{ $likesCount }} لایک</div>
    @if($todayLikes > 0)
        <p class="text-sm text-green-600 font-medium mb-4">
            +{{ $todayLikes }} لایک جدید امروز
        </p>
    @endif

    <div class="grid grid-cols-5 gap-3 mt-6">
        @foreach($latestLikers as $like)
            <div class="text-center">
                <a href="{{ route('profile.show', $like->liker->id) }}">
                    @if($like->liker->profile_picture)
                        <img src="{{ asset('storage/' . $like->liker->profile_picture) }}"
                             class="w-16 h-16 rounded-full object-cover border-2 border-red-400">
                    @else
                        <div class="w-16 h-16 rounded-full bg-gray-300 flex items-center justify-center">
                            <span class="text-2xl">👤</span>
                        </div>
                    @endif
                    <p class="text-xs mt-1 truncate">{{ $like->liker->name }}</p>
                </a>
            </div>
        @endforeach
    </div>

    @if($likesCount > 5)
        <a href="{{ route('likes.received') }}" class="block mt-4 text-center text-red-600 font-medium">
            دیدن همه لایک‌ها →
        </a>
    @endif
</div>
             </div>
         </div>
         <div class="animate-slide-in" style="animation-delay: 0.6s;">
             <h3 class="text-2xl font-bold text-gray-800 mb-4">مناطق</h3>
             <div class="card p-4 shadow rounded-lg">
    <h3 class="text-lg font-bold mb-2">بازدید از پروفایل شما</h3>
    <p>👁️ امروز: <strong>{{ $todayViews }}</strong> بازدید</p>
    <p>📈 کل بازدیدها: <strong>{{ $totalViews }}</strong></p>

    <h4 class="mt-3 font-semibold">سه بازدیدکننده اخیر:</h4>
    <ul>
        @forelse($latestViewers as $view)
            <li>{{ $view->viewer->name ?? 'کاربر ناشناس' }}</li>
        @empty
            <li>هنوز کسی پروفایل شما را ندیده است.</li>
        @endforelse
    </ul>

    <a href="#" class="text-blue-600 hover:underline mt-2 inline-block">
        مشاهده لیست کامل
    </a>
</div>
<div class="animate-slide-in z-0" style="animation-delay: 0.7s;">
    <h3 class="text-2xl font-bold text-gray-800 mb-4">اعضای فعال</h3>
    <div class="bg-white shadow-lg rounded-xl p-6">
        <div class="flex flex-row justify-around items-start space-x-reverse space-x-6 overflow-x-auto">
            @foreach ($recentUsers as $recentUser)
                <div class="flex flex-col items-center flex-shrink-0 min-w-[100px]">
                    <a href="{{ route('profile.show', $recentUser->id) }}">
                        <img src="{{ asset('storage/' . ($recentUser->profile_picture ?? 'default.jpg')) }}"
                             alt="{{ $recentUser->name ?? 'کاربر' }}"
                             class="w-20 h-20 rounded-full object-cover mb-3 border-2 border-white shadow-lg hover:scale-110 transition-transform duration-300 mx-auto">
                    </a>
                    <span class="text-sm text-gray-600 text-center block px-1">{{ $recentUser->name ?? 'کاربر' }}</span>
                </div>
            @endforeach
        </div>
    </div>
</div>

     <!-- بازدیدکنندگان اخیر -->
     <div class="mt-8 animate-slide-in z-0" style="animation-delay: 1.0s;">
         <h3 class="text-2xl font-bold text-gray-800 mb-4">بازدیدکنندگان اخیر</h3>
         <div class="bg-white card p-6 rounded-xl">
             @forelse($recentProfileViews as $view)
                 <div class="flex items-center space-x-reverse space-x-4 border-b py-2">
                     <img src="{{ asset('storage/' . ($view->viewer->profile_picture ?? 'default.jpg')) }}"
                         class="w-10 h-10 rounded-full" alt="{{ $view->viewer->name }}">
                     <div>
                         <a href="{{ route('profile.show', $view->viewer->id) }}"
                             class="text-pink-600 hover:underline font-bold">
                             {{ $view->viewer->name }}
                         </a>
                         <p class="text-sm text-gray-500">{{ $view->created_at->diffForHumans() }}</p>
                     </div>
                 </div>
             @empty
                 <p class="text-gray-500">هنوز کسی پروفایل شما را مشاهده نکرده است.</p>
             @endforelse
         </div>
     </div>

     <!-- آمار پایینی -->
     <div class="mt-8 bg-gray-800 text-white p-6 rounded-xl animate-slide-in" style="animation-delay: 0.9s;">
         <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
             <div>
                 <h4 class="text-lg font-bold">جمعیت</h4>
                 <p class="text-gray-300">کشور / شهر</p>
             </div>
             <div>
                 <h4 class="text-lg font-bold">سیستم</h4>
                 <p class="text-gray-300">مرورگر / سیستم‌عامل</p>
             </div>
             <div>
                 <h4 class="text-lg font-bold">هدف</h4>
                 <p class="text-gray-300">علایق کاربران</p>
             </div>
         </div>
     </div>

     <footer class="mt-8 bg-gray-100 p-6 rounded-xl text-center animate-slide-in" style="animation-delay: 1s;">
         <p class="text-gray-600">ساخته شده با Laravel و Tailwind CSS</p>
     </footer>
         </div>
     </div>

 
 </main>
