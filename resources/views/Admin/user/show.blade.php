@extends('layouts.app') {{-- فرض بر اینکه layout کلی داری و بوت‌استرپ اضافه شده --}}

@section('content')
    <div class="container mt-5">
        <div class="card shadow-lg mb-4">
            <div class="card-body">
                <div class="d-flex align-items-center mb-4">
                    <img src="{{ $user->avatar_url ?? asset('images/default-avatar.png') }}" alt="avatar"
                        class="rounded-circle border border-primary shadow" width="128" height="128">
                    <div class="ms-4">
                        <h2 class="h4 fw-bold text-dark">{{ $user->name }}</h2>
                        <p class="mb-1 text-muted">{{ $user->email }}</p>
                        <p class="text-secondary small">عضو شده در {{ $user->created_at->format('Y/m/d') }}</p>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="alert alert-primary">
                            <strong>سن:</strong> {{ $user->age ?? '---' }}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="alert alert-info">
                            <strong>جنسیت:</strong> {{ $user->gender ?? '---' }}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="alert alert-warning">
                            <strong>شماره تماس:</strong> {{ $user->phone ?? '---' }}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="alert alert-danger">
                            <strong>وضعیت:</strong>
                            @if ($user->banned)
                                <span class="fw-bold text-danger">مسدود شده</span>
                            @else
                                <span class="fw-bold text-success">فعال</span>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="alert alert-success">
                            <strong>IP آخرین ورود:</strong> {{ $user->last_login_ip ?? '---' }}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="alert alert-secondary">
                            <strong>شهر:</strong> {{ $user->city ?? '---' }}
                        </div>
                    </div>
                </div>

                <div class="mt-4 p-3 border rounded bg-light">
                    <h5 class="mb-2">بیوگرافی:</h5>
                    <p>{{ $user->bio ?? 'ندارد' }}</p>
                </div>

                <div class="row text-center mt-4 g-3">
                    <div class="col-md-3">
                        <div class="p-3 bg-light border rounded">
                            <h5>{{ $user->messages_count ?? 0 }}</h5>
                            <small class="text-muted">پیام‌ها</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-3 bg-light border rounded">
                            <h5>{{ $user->likes_count ?? 0 }}</h5>
                            <small class="text-muted">لایک‌ها</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-3 bg-light border rounded">
                            <h5>{{ $user->reports_count ?? 0 }}</h5>
                            <small class="text-muted">ریپورت‌ها</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-3 bg-light border rounded">
                            <h5>{{ $user->visits_count ?? 0 }}</h5>
                            <small class="text-muted">بازدید‌ها</small>
                        </div>
                    </div>
                </div>

                <div class="mt-4 d-flex justify-content-end gap-2">
                    @if ($user->banned)
                        <form method="POST" action="{{ route('admin.users.unban', $user) }}">
                            @csrf
                            <button class="btn btn-success">آزادسازی حساب</button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('admin.users.ban', $user) }}">
                            @csrf
                            <button class="btn btn-danger">بن کردن کاربر</button>
                        </form>
                    @endif

                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-dark" onclick="return confirm('آیا مطمئنی؟')">حذف کاربر</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
