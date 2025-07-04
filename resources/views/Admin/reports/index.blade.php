@extends('adminlte::page')

@section('title', 'مدیریت گزارش‌ها')

@section('content_header')
    <h1>مدیریت گزارش‌ها</h1>
@endsection

@section('content')

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>شناسه</th>
                <th>گزارش‌دهنده</th>
                <th>کاربر گزارش‌شده</th>
                <th>دلیل</th>
                <th>تاریخ</th>
                <th>عملیات</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($reports as $report)
                <tr>
                    <td>{{ $report->id }}</td>
                    <td>{{ $report->reporter->name ?? 'نامشخص' }}</td>
                    <td>{{ $report->reported->name ?? 'نامشخص' }}</td>
                    <td>{{ $report->reason ?? 'بدون دلیل' }}</td>
                    <td>{{ $report->created_at->format('Y-m-d H:i') }}</td>
                    <td>
                        @if (!$report->resolved)
                            <form action="{{ route('admin.reports.resolve', $report) }}" method="POST">
                                @csrf
                                <button class="btn btn-success btn-sm" onclick="return confirm('بررسی شد؟')">بررسی شد</button>
                            </form>
                        @else
                            <span class="badge bg-success">بررسی شده</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">هیچ گزارشی ثبت نشده است.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="d-flex justify-content-center mt-4">
        {!! $reports->links() !!}
    </div>

@endsection
