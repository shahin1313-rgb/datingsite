<div class="max-w-4xl mx-auto mt-12">
    <h2
        class="text-3xl font-extrabold text-gray-800 mb-8 flex items-center gap-3"
    >
        <i class="fa fa-shield text-pink-500 text-2xl"></i>

        <span class="border-b-4 border-pink-500 pb-1">
            لیست کاربران بلاک شده
        </span>
    </h2>

    @if ($blockedUsers->isEmpty())
        <div
            class="bg-gradient-to-r from-gray-50 to-gray-100 shadow-lg rounded-xl p-8 text-center"
        >
            <p class="text-gray-600 text-lg">
                هیچ کاربری بلاک نشده است.
            </p>
        </div>
    @else
        <div class="grid gap-5">
            @foreach ($blockedUsers as $block)
                <div
                    class="bg-white shadow-md rounded-xl p-5 flex items-center justify-between border border-gray-100 hover:shadow-xl hover:-translate-y-1 transform transition-all duration-300"
                >
                    <div class="flex items-center gap-4">
                        <div class="relative">
                            <img
                                src="{{ $block->blocked->profilePhotoUrl() }}"
                                alt="{{ $block->blocked->name ?? 'کاربر' }}"
                                class="w-14 h-14 rounded-full object-cover border-2 border-pink-200 shadow-sm"
                            >

                            <span
                                class="absolute bottom-0 right-0 w-4 h-4 bg-red-500 border-2 border-white rounded-full"
                            ></span>
                        </div>

                        <div>
                            <p
                                class="font-semibold text-gray-800 text-lg"
                            >
                                {{ $block->blocked->name ?? 'کاربر حذف‌شده' }}
                            </p>

                            <p class="text-sm text-gray-500 mt-1">
                                ID:
                                {{ $block->blocked->id ?? '-' }}
                            </p>
                        </div>
                    </div>

                    @if($block->blocked)
                        <form
                            action="{{ route('user.unblock', $block->blocked->id) }}"
                            method="POST"
                            data-confirm="آیا مطمئن هستید؟"
                        >
                            @csrf

                            <button
                                type="submit"
                                class="flex items-center gap-2 bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white px-5 py-2.5 rounded-lg shadow-md hover:shadow-lg transition-all duration-300 text-sm font-medium"
                            >
                                <i class="fa fa-unlock"></i>
                                آن‌بلاک
                            </button>
                        </form>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
</div>
