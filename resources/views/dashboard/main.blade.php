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
             <h3 class="text-2xl font-bold text-gray-800 mb-4">فعالیت‌های اخیر</h3>
             <div class="bg-white card p-6 rounded-xl">
                 <table class="w-full text-right">
                     <tbody>
                         <tr class="border-b">
                             <td class="py-3"><i class="fa fa-user text-blue-500 ml-2"></i> مشاهده جدید</td>
                             <td class="py-3 text-gray-600">10 دقیقه قبل</td>
                         </tr>
                         <tr class="border-b">
                             <td class="py-3"><i class="fa fa-bell text-red-500 ml-2"></i> هشدار سیستم</td>
                             <td class="py-3 text-gray-600">15 دقیقه قبل</td>
                         </tr>
                     </tbody>
                 </table>
             </div>
         </div>
         <div class="animate-slide-in" style="animation-delay: 0.6s;">
             <h3 class="text-2xl font-bold text-gray-800 mb-4">مناطق</h3>
             <div class="bg-white card p-6 rounded-xl">
                 <table class="w-full text-right">
                     <tbody>
                         <tr class="border-b">
                             <td class="py-3">آمریکا</td>
                             <td class="py-3 text-gray-600">65%</td>
                         </tr>
                         <tr>
                             <td class="py-3">انگلستان</td>
                             <td class="py-3 text-gray-600">15%</td>
                         </tr>
                     </tbody>
                 </table>
             </div>
         </div>
     </div>

     <!-- اعضای فعال -->
     <div class="animate-slide-in z-0" style="animation-delay: 0.7s;">
         <h3 class="text-2xl font-bold text-gray-800 mb-4">اعضای فعال</h3>
         <div class="bg-white shadow-lg rounded-xl p-6 customer-logos">
             <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                 @foreach ($recentUsers as $recentUser)
                     <div class="flex flex-col items-center">
                         <a href="{{ route('profile.show', $recentUser->id) }}">
                             <img src="{{ asset('storage/' . ($recentUser->profile_picture ?? 'default.jpg')) }}"
                                 alt="{{ $recentUser->name ?? 'کاربر' }}"
                                 class="w-24 h-24 rounded-full object-cover mb-3 border-2 border-white shadow-lg hover:scale-110 transition-transform duration-300">
                         </a>
                         <span class="text-sm text-gray-600 text-center">{{ $recentUser->name ?? 'کاربر' }}</span>
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
 </main>
