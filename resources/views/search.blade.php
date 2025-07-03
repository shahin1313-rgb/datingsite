@extends('layouts.app')

@section('content')
    <div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh;">
        <div class="row justify-content-center w-100">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('Dashboard') }}</div>

                    <h1 class="text-center mt-3">Search Profiles</h1>
                    <form action="{{ route('search') }}" method="GET" class="p-3">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="city">City:</span>
                            </div>
                            <input type="text" class="form-control" placeholder="city" name="city" id="city"
                                value="{{ request('city') }}" aria-label="city" aria-describedby="basic-addon1">
                        </div>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="age">Min Age:</span>
                            </div>
                            <input type="number" name="min_age" id="min_age" value="{{ request('min_age') }}"
                                class="form-control" placeholder="min age" aria-label="age" aria-describedby="basic-addon1">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="age">Max Age:</span>
                            </div>
                            <input type="number" name="max_age" id="max_age" value="{{ request('max_age') }}"
                                class="form-control" placeholder="max age" aria-label="age" aria-describedby="basic-addon1">
                        </div>
                        <div class="input-group-prepend">
                            <button class="btn btn-outline-secondary" type="submit">Search</button>
                        </div>
                    </form>

                    <div class="card align-items-center mx-auto" style="width: 14rem;">
                        @if ($profiles->isEmpty())
                            <p>No profiles found.</p>
                        @else
                            <ul class="list-unstyled p-3">
                                @foreach ($profiles as $profile)
                                    <li class="mb-3">
                                        @if ($profile->profile_picture)
                                            {{-- <img src="{{ $profile->profile_picture ? asset('storage/' . $profile->profile_picture) : asset('images/default-profile.jpg') }}"

                                            class="card-img-top" alt="Profile Picture"> --}}
                                            <img class="rounded-circle shadow-4-strong card-img-top" alt="avatar2"
                                                src="{{ asset('storage/' . $profile->profile_picture) }}" />
                                        @else
                                            <p>No profile picture available.</p>
                                        @endif

                                        <div class="card-body">
                                            <h5 class="card-title">{{ $profile->name }}</h5>
                                            <h5 class="card-title">{{ $profile->city }}</h5>
                                            <h5 class="card-title">{{ $profile->age }}</h5>
                                            <h5 class="card-title">{{ $profile->birth_year }}</h5>
                                            <p class="card-text">{{ $profile->email }}</p>
                                            <p class="card-text">{{ $profile->bio }}</p>

                                            <div class="d-flex gap-2">
                                                <a href="{{ route('messages.show', $profile) }}" class="btn btn-primary ">
                                                    Send Message
                                                </a>

                                                <a href="{{ route('profile.show', $profile->id) }}"
                                                    class="btn btn-primary">View Profile</a>

                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>

                            <div class="">
                                <!-- Pagination Links -->
                                {{-- {{ $profiles->appends(request()->query())->links() }} --}}

                                <div class="d-flex justify-content-center mt-4">
                                    <nav>
                                        <ul class="pagination">
                                            {{ $profiles->appends(request()->query())->links('pagination::bootstrap-4') }}
                                        </ul>
                                    </nav>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
