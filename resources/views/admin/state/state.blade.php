@extends('adminlte::page')

@section('title', 'داشبورد مدیریت')

@section('content_header')
    <h1>داشبورد مدیریت</h1>
@endsection

@section('content')
    <div class="row">
        <!-- کارت آمار کاربران -->
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

        <!-- کارت آمار پیام‌ها -->
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

        <!-- کارت آمار ریپورت‌ها -->
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

        <!-- کارت بازدیدها -->
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

    <!-- چارت -->
    <div class="card mt-4">
        <div class="card-header">
            <h3 class="card-title">آمار ثبت‌نام هفتگی</h3>
        </div>
        <div class="card-body">
            <canvas id="registrationChart" height="100"></canvas>
        </div>
    </div>
@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('registrationChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($chartLabels) !!},
                datasets: [{
                    label: 'کاربران جدید',
                    data: {!! json_encode($chartData) !!},
                    borderColor: 'rgba(54, 162, 235, 1)',
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
@endsection
