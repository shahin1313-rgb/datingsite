<div class="container mx-auto px-4 py-10">

    <!-- فرم ارسال تیکت -->
    <div
        class="max-w-2xl mx-auto bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 p-[2px] rounded-2xl shadow-xl mb-10">
        <div class="bg-white rounded-2xl p-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                <i class="fa fa-envelope-open text-pink-500 ml-2"></i> ارسال تیکت جدید
            </h2>
            <form action="{{ route('user.tickets.store') }}" method="POST">
                @csrf
                <div class="mb-5">
                    <label for="subject" class="block text-gray-700 font-semibold mb-2">موضوع</label>
                    <input type="text" name="subject" id="subject"
                        class="w-full border border-gray-300 rounded-xl px-4 py-2 focus:outline-none focus:ring-2 focus:ring-pink-400 transition"
                        placeholder="موضوع تیکت را وارد کنید..." required>
                </div>
                <div class="mb-5">
                    <label for="message" class="block text-gray-700 font-semibold mb-2">پیام</label>
                    <textarea name="message" id="message" rows="5"
                        class="w-full border border-gray-300 rounded-xl px-4 py-2 focus:outline-none focus:ring-2 focus:ring-pink-400 transition"
                        placeholder="پیام خود را بنویسید..." required></textarea>
                </div>
                <button type="submit"
                    class="w-full bg-gradient-to-r from-pink-500 to-red-500 text-white font-bold py-3 rounded-xl hover:opacity-90 transition transform hover:scale-[1.02]">
                    <i class="fa fa-paper-plane ml-2"></i> ارسال تیکت
                </button>
            </form>
        </div>
    </div>

    <!-- لیست تیکت‌ها -->
    <div class="max-w-3xl mx-auto">
        <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
            <i class="fa fa-list text-indigo-500 ml-2"></i> لیست تیکت‌های من
        </h2>

        @forelse ($tickets as $ticket)
            <div
                class="bg-white p-5 rounded-xl shadow-lg mb-5 hover:shadow-2xl transition transform hover:scale-[1.01]">
                <h3 class="text-lg font-bold text-gray-800 flex items-center">
                    <i class="fa fa-tag text-pink-500 ml-2"></i> {{ $ticket->subject }}
                </h3>
                <p class="text-gray-600 mt-2">{{ $ticket->message }}</p>
                <div class="mt-3 flex items-center justify-between text-sm">
                    <span class="text-gray-400">
                        <i class="fa fa-clock ml-1"></i> {{ $ticket->created_at->diffForHumans() }}
                    </span>
                    <span
                        class="px-3 py-1 rounded-full text-sm font-semibold
                        {{ $ticket->status === 'open' ? 'bg-green-100 text-green-600' : 'bg-gray-200 text-gray-600' }}">
                        {{ $ticket->status === 'open' ? 'باز' : 'بسته' }}
                    </span>
                </div>
            </div>
        @empty
            <p class="text-gray-500 text-center mt-6">هیچ تیکتی ثبت نشده است.</p>
        @endforelse
    </div>

</div>
