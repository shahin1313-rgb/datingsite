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

    <a href="#"
       class="bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-4 rounded">
        {{ __('profilepage.show') }}
    </a>

    <form action="{{ route('like.store', $profileOwner->id) }}" method="POST">
        @csrf
        <button type="submit" class="bg-red-500 text-white px-3 py-1 rounded">
            ❤️ {{ __('profilepage.like') }}
        </button>
    </form>

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
