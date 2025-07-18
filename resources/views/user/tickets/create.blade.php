<div class="max-w-xl mx-auto mt-10 bg-white p-6 rounded shadow">
    <h2 class="text-xl font-bold mb-4">ارسال تیکت پشتیبانی</h2>
    <form action="{{ route('user.tickets.store') }}" method="POST">
        @csrf
        <div class="mb-4">
            <label class="block mb-1">موضوع</label>
            <input type="text" name="subject" class="w-full border p-2 rounded" required>
        </div>
        <div class="mb-4">
            <label class="block mb-1">پیام</label>
            <textarea name="message" class="w-full border p-2 rounded" rows="5" required></textarea>
        </div>
        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
            ارسال
        </button>
    </form>
</div>
