@extends('layouts.app')

@section('content')
    <div class="min-h-screen flex justify-center items-center px-4">
        <div class="w-full max-w-4xl">
            <div class="bg-white shadow-md rounded-lg overflow-hidden">

                {{-- Header --}}
                <div class="bg-gray-100 px-6 py-4 border-b border-gray-200 text-xl font-semibold">
                    {{ __('Dashboard') }}
                </div>

                {{-- Search Form --}}
                <h1 class="text-2xl text-center mt-6 font-bold">Search Profiles</h1>

                <form action="{{ route('search') }}" method="GET" class="p-6 space-y-4 text-right">

                    {{-- City --}}
                    <div>
                        <label for="city" class="block text-gray-700 font-medium mb-1">شهر:</label>
                        <input type="text" id="city" name="city" value="{{ request('city') }}"
                            placeholder="مثلاً تهران"
                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    {{-- Age Range --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="min_age" class="block text-gray-700 font-medium mb-1">حداقل سن:</label>
                            <input type="number" id="min_age" name="min_age" value="{{ request('min_age') }}"
                                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label for="max_age" class="block text-gray-700 font-medium mb-1">حداکثر سن:</label>
                            <input type="number" id="max_age" name="max_age" value="{{ request('max_age') }}"
                                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>

                    {{-- Marital Status --}}
                    <div>
                        <label for="marital_status" class="block text-gray-700 font-medium mb-1">وضعیت تأهل:</label>
                        <select name="marital_status" id="marital_status"
                            class="w-full border border-gray-300 rounded-md px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">-- انتخاب کنید --</option>
                            <option value="single" {{ request('marital_status') == 'single' ? 'selected' : '' }}>مجرد
                            </option>
                            <option value="married" {{ request('marital_status') == 'married' ? 'selected' : '' }}>متأهل
                            </option>
                            <option value="divorced" {{ request('marital_status') == 'divorced' ? 'selected' : '' }}>جدا شده
                            </option>
                        </select>
                    </div>

                    {{-- Common Interests --}}
                    <div>
                        <label for="interests" class="block text-gray-700 font-medium mb-1">علایق:</label>
                        <input type="text" name="interests" id="interests" value="{{ request('interests') }}"
                            placeholder="مثلاً موسیقی، ورزش"
                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    {{-- With Photo --}}
                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="has_photo" id="has_photo" value="1"
                            {{ request('has_photo') ? 'checked' : '' }}
                            class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded">
                        <label for="has_photo" class="text-gray-700">فقط کاربران دارای عکس</label>
                    </div>

                    {{-- Only Active Users --}}
                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="is_active" id="is_active" value="1"
                            {{ request('is_active') ? 'checked' : '' }}
                            class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded">
                        <label for="is_active" class="text-gray-700">فقط کاربران فعال</label>
                    </div>

                    {{-- Search Button --}}
                    <div class="text-center mt-4">
                        <button type="submit"
                            class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 transition duration-200">
                            جستجو
                        </button>
                    </div>

                </form>

                {{-- Search Results --}}
                <div class="p-6">
                    @if ($profiles->isEmpty())
                        <p class="text-center text-gray-500">No profiles found.</p>
                    @else
                        <ul class="space-y-6">
                            @foreach ($profiles as $profile)
                                <li class="border border-gray-200 rounded-lg p-4 flex flex-col items-center">
                                    @if ($profile->profile_picture)
                                        <img src="{{ asset('storage/' . $profile->profile_picture) }}" alt="avatar"
                                            class="w-24 h-24 rounded-full shadow-md mb-3 object-cover" />
                                    @else
                                        <p class="text-sm text-gray-400">No profile picture available.</p>
                                    @endif

                                    <div class="text-center">
                                        <h5 class="text-lg font-bold">{{ $profile->name }}</h5>
                                        <p class="text-sm text-gray-600">
                                            {{ $profile->city }} | Age: {{ $profile->age }} | Born:
                                            {{ $profile->birth_year }}
                                        </p>
                                        <p class="text-sm mt-1 text-gray-500">{{ $profile->email }}</p>
                                        <p class="text-sm mt-1 text-gray-500">{{ $profile->bio }}</p>

                                        <div class="flex justify-center gap-3 mt-4">
                                            <a href="{{ route('messages.show', $profile) }}"
                                                class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm">
                                                Send Message
                                            </a>
                                            <a href="{{ route('profile.show', $profile->id) }}"
                                                class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700 text-sm">
                                                View Profile
                                            </a>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>

                        {{-- Pagination --}}
                        <div class="mt-6 flex justify-center">
                            {{ $profiles->appends(request()->query())->links() }}
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </div>
@endsection
