@extends('layouts.app')
@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-center">
            <div class="w-full md:w-2/3 lg:w-1/2">
                <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                    <div class="px-6 py-4 border-b">
                        <h2 class="text-2xl font-semibold">{{ trans('Dashboard') }}</h2>
                    </div>
                    <div class="p-6">
                        @if (session('status'))
                            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4"
                                role="alert">
                                {{ session('status') }}
                            </div>
                        @endif
                        @if ($profileOwner->profile_picture)
                            <div class="flex justify-center mt-4">


                                <img src="{{ asset('storage/' . $profileOwner->profile_picture) }}"
                                    class="w-48 h-48 rounded-full border-4 border-white shadow-xl object-cover -mt-16 z-10"
                                    alt="Profile Picture">
                            </div>
                        @else
                            <div class="flex justify-center mt-4">
                                <div
                                    class="w-48 h-48 rounded-full bg-gray-200 flex items-center justify-center text-gray-500 text-sm border-4 border-white shadow-xl -mt-16 z-10">
                                    No Image
                                </div>
                            </div>
                        @endif


<div class="p-4 space-y-3">

    <p><strong>{{ __('profilepage.name') }}:</strong> {{ $profileOwner->name }}</p>

    <p><strong>{{ __('profilepage.city') }}:</strong> {{ $profileOwner->city }}</p>

    <p><strong>{{ __('profilepage.email') }}:</strong> {{ $profileOwner->email }}</p>

    <p>
        <strong>{{ __('profilepage.marital_status') }}:</strong>
        @if ($profileOwner->marital_status == 'single')
            {{ __('profilepage.single') }}
        @elseif($profileOwner->marital_status == 'married')
            {{ __('profilepage.married') }}
        @elseif($profileOwner->marital_status == 'divorced')
            {{ __('profilepage.divorced') }}
        @elseif($profileOwner->marital_status == 'widowed')
            {{ __('profilepage.widowed') }}
        @else
            {{ __('profilepage.not_set') }}
        @endif
    </p>

    <p><strong>{{ __('profilepage.salary') }}:</strong> {{ $profileOwner->salary }} {{ __('profilepage.salary_unit') }}</p>

    <p><strong>{{ __('profilepage.bio') }}:</strong> {{ $profileOwner->bio }}</p>

    <p><strong>{{ __('profilepage.role') }}:</strong> {{ $profileOwner->role }}</p>

</div>

  <div class="mt-4 flex flex-wrap gap-2">

    <a href="{{ url()->previous() }}"
       class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded">
        {{ __('profilepage.back') }}
    </a>

    

    {{-- ===== دکمه لایک با Ajax ===== --}}
<div class="relative">
    <form id="likeForm" action="{{ route('like.store', $profileOwner->id) }}" method="POST">
        @csrf

        {{-- بررسی کن کاربر قبلاً لایک کرده یا نه --}}
        @php
$alreadyLiked = auth()->check() && \DB::table('likes')
        ->where('user_id', auth()->id())
        ->where('liked_user_id', $profileOwner->id)
        ->exists();
        
        @endphp

        <button type="submit"
                id="likeButton"
                class="like-btn flex items-center gap-2 px-5 py-2 rounded-lg font-medium text-white transition-all duration-300 {{ $alreadyLiked ? 'bg-green-600 hover:bg-green-700' : 'bg-red-500 hover:bg-red-600' }}"
                {{ $alreadyLiked ? 'disabled' : '' }}>
            
            <span id="likeIcon">{{ $alreadyLiked ? '✓' : '❤️' }}</span>
            <span id="likeText">
                {{ $alreadyLiked ? 'لایک شده' : __('profilepage.like') }}
            </span>
        </button>
    </form>
</div>

    @if (auth()->check() && auth()->id() !== $profileOwner->id)
        <form action="{{ route('report.store') }}" method="POST">
            @csrf
            <input type="hidden" name="reported_id" value="{{ $profileOwner->id }}">
            <button type="submit"
                class="bg-red-500 hover:bg-red-600 text-white font-medium py-2 px-4 rounded">
                {{ __('profilepage.quick_report') }} 🚫
            </button>
        </form>

        <button onclick="document.getElementById('reportModal').classList.remove('hidden')"
            class="bg-yellow-500 hover:bg-yellow-600 text-white font-medium py-2 px-4 rounded">
            ⚠️ {{ __('profilepage.suspicious_behavior') }}
        </button>
    @endif

</div>

</div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

    <!-- Report Modal -->
    <div id="reportModal" class="fixed inset-0 z-50 flex items-center justify-center hidden">
        <div class="absolute inset-0 bg-black opacity-50"></div>
        <div class="bg-white rounded-lg shadow-xl z-50 w-full max-w-md mx-4">
            <div class="flex justify-between items-center px-4 py-3 border-b">
                <h3 class="text-lg font-semibold">Report User</h3>
                <button type="button" class="text-gray-600 hover:text-gray-800"
                    onclick="document.getElementById('reportModal').classList.add('hidden')">
                    &times;
                </button>
            </div>
            <div class="px-4 py-4">
                <form action="{{ route('report.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="reported_id" value="{{ $profileOwner->id }}">
                    <label for="reason" class="block text-sm font-medium text-gray-700">Reason for reporting:</label>
                    <textarea name="reason" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" required></textarea>
                    <button type="submit"
                        class="mt-3 bg-red-500 hover:bg-red-600 text-white font-medium py-2 px-4 rounded">
                        Submit Report
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection

{{-- اسکریپت Ajax خیلی ساده - فقط در همین صفحه کار می‌کنه --}}
<script>
document.getElementById('likeForm')?.addEventListener('submit', function(e) {
    e.preventDefault();

    const button   = document.getElementById('likeButton');
    const icon     = document.getElementById('likeIcon');
    const text     = document.getElementById('likeText');

    // غیرفعال کردن موقت دکمه
    button.disabled = true;

    fetch(this.action, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: new FormData(this)
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            // تغییر ظاهر به حالت لایک شده
            button.classList.remove('bg-red-500', 'hover:bg-red-600');
            button.classList.add('bg-green-600', 'hover:bg-green-700');
            icon.textContent = '✓';
            text.textContent = 'لایک شده';
            button.disabled = true; // دیگه نتونه دوباره لایک کنه
        }
        اگر بخوای آنلایک هم بشه، فقط این else رو فعال کن
        else if (result.liked === false) {
            button.classList.remove('bg-green-600'); 
            button.classList.add('bg-red-500');
            icon.textContent = '❤️';
            text.textContent = 'لایک';
            button.disabled = false;
        }
    })
    .catch(() => {
        alert('خطایی رخ داد، دوباره تلاش کنید');
        button.disabled = false;
    });
});
</script>