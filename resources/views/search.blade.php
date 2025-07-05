@extends('layouts.app')

@section('content')
    <div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh;">
        <div class="row justify-content-center w-100">
            <div class="col-md-8">
                <div class="card">

                    {{-- Header --}}
                    <div class="card-header">{{ __('Dashboard') }}</div>

                    {{-- Search Form --}}
                    <h1 class="text-center mt-3">Search Profiles</h1>

                    <form action="{{ route('search') }}" method="GET" class="p-3">
                        {{-- City --}}
                        <div class="form-group mb-3">
                            <label for="city">City:</label>
                            <input type="text" class="form-control" id="city" name="city"
                                placeholder="Enter city" value="{{ request('city') }}">
                        </div>

                        {{-- Age Range --}}
                        <div class="form-row mb-3">
                            <div class="col-md-6 mb-2">
                                <label for="min_age">Min Age:</label>
                                <input type="number" class="form-control" id="min_age" name="min_age"
                                    value="{{ request('min_age') }}" placeholder="Minimum age">
                            </div>
                            <div class="col-md-6 mb-2">
                                <label for="max_age">Max Age:</label>
                                <input type="number" class="form-control" id="max_age" name="max_age"
                                    value="{{ request('max_age') }}" placeholder="Maximum age">
                            </div>
                        </div>

                        {{-- Search Button --}}
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary">Search</button>
                        </div>
                    </form>

                    {{-- Search Results --}}
                    <div class="card align-items-center mx-auto my-4" style="width: 14rem;">

                        @if ($profiles->isEmpty())
                            <p class="p-3">No profiles found.</p>
                        @else
                            <ul class="list-unstyled p-3">
                                @foreach ($profiles as $profile)
                                    <li class="mb-4">
                                        @if ($profile->profile_picture)
                                            <img class="rounded-circle shadow-4-strong card-img-top mb-2" alt="avatar"
                                                src="{{ asset('storage/' . $profile->profile_picture) }}" />
                                        @else
                                            <p>No profile picture available.</p>
                                        @endif

                                        <div class="card-body text-center">
                                            <h5 class="card-title">{{ $profile->name }}</h5>
                                            <h6>{{ $profile->city }} | Age: {{ $profile->age }} | Born:
                                                {{ $profile->birth_year }}</h6>
                                            <p class="card-text">{{ $profile->email }}</p>
                                            <p class="card-text">{{ $profile->bio }}</p>

                                            <div class="d-flex justify-content-center gap-2 mt-2">
                                                <a href="{{ route('messages.show', $profile) }}"
                                                    class="btn btn-primary btn-sm">
                                                    Send Message
                                                </a>
                                                <a href="{{ route('profile.show', $profile->id) }}"
                                                    class="btn btn-secondary btn-sm">
                                                    View Profile
                                                </a>
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>

                            {{-- Pagination --}}
                            <div class="d-flex justify-content-center mt-3">
                                {{ $profiles->appends(request()->query())->links('pagination::bootstrap-4') }}
                            </div>
                        @endif

                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
