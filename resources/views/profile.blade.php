@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('Dashboard') }}</div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        <div class="card" style="width: 18rem;">
                            @if ($user->profile_picture)
                                <img src="{{ $user->profile_picture ? asset('storage/' . $user->profile_picture) : asset('images/default-profile.jpg') }}"
                                    class="card-img-top" alt="Profile Picture">
                            @else
                                <p>No profile picture available.</p>
                            @endif

                            <div class="card-body">
                                <h5 class="card-title">{{ $user->name }}</h5>
                                <h5 class="card-title">{{ $user->city }}</h5>

                                <p class="card-text">{{ $user->email }}</p>
                                <p>Interested In: {{ $user->interested_in }}</p>
                                <p>Salary: میلیون تومن {{ $user->salary }}</p>
                                <p class="card-text">{{ $user->bio }}</p>
                                <p>Role: {{ $user->role }}</p>
                                <a href="{{ url()->previous() }}" class="btn btn-secondary">Back</a>


                                <a href="#" class="btn btn-primary">show</a>


                                @if (auth()->check() && auth()->id() !== $user->id)
                                    <form action="{{ route('report.store') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="reported_id" value="{{ $user->id }}">

                                        <button type="submit" class="btn btn-danger btn-sm">گزارش</button>
                                    </form>
                                @endif

                                <!-- Report Modal -->
                                <div class="modal fade" id="reportModal" tabindex="-1" aria-labelledby="reportModalLabel"
                                    aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="reportModalLabel">Report User</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <form action="{{ route('report.store') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="reported_id" value="{{ $user->id }}">
                                                    <label for="reason">Reason for reporting:</label>
                                                    <textarea name="reason" class="form-control" required></textarea>
                                                    <button type="submit" class="btn btn-danger mt-3">Submit
                                                        Report</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
