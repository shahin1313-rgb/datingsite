@extends('adminlte::page')

@section('title', 'داشبورد مدیریت')

@section('content_header')
    <h1>داشبورد مدیریت</h1>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-3">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $userCount }}</h3>
                    <p>تعداد کاربران</p>
                </div>

                <div class="icon">
                    <i class="fas fa-users"></i>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $messageCount }}</h3>
                    <p>تعداد پیام‌ها</p>
                </div>

                <div class="icon">
                    <i class="fas fa-envelope"></i>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $reportCount }}</h3>
                    <p>تعداد ریپورت‌ها</p>
                </div>

                <div class="icon">
                    <i class="fas fa-flag"></i>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $visitCount }}</h3>
                    <p>بازدیدهای سایت</p>
                </div>

                <div class="icon">
                    <i class="fas fa-eye"></i>
                </div>
            </div>
        </div>
    </div>

    @php
        $maxChartValue = max(
            1,
            (int) collect($chartData)->max()
        );
    @endphp

    <div class="card mt-4">
        <div class="card-header">
            <h3 class="card-title">
                آمار ثبت‌نام هفتگی
            </h3>
        </div>

        <div class="card-body">
            @forelse ($chartLabels as $index => $label)
                @php
                    $value =
                        (int) ($chartData[$index] ?? 0);

                    $width =
                        ($value / $maxChartValue) * 100;
                @endphp

                <div class="mb-3">
                    <div
                        class="d-flex justify-content-between mb-1"
                    >
                        <span>{{ $label }}</span>
                        <strong>{{ $value }}</strong>
                    </div>

                    <div
                        class="progress"
                        style="height: 18px;"
                    >
                        <div
                            class="progress-bar bg-primary"
                            role="progressbar"
                            style="width: {{ $width }}%;"
                            aria-valuenow="{{ $value }}"
                            aria-valuemin="0"
                            aria-valuemax="{{ $maxChartValue }}"
                        ></div>
                    </div>
                </div>
            @empty
                <p class="text-muted mb-0">
                    داده‌ای برای نمایش وجود ندارد.
                </p>
            @endforelse
        </div>
    </div>
@endsection