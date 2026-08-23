@extends('adminlte::page')

@section('title', 'مدیریت گزارش‌ها')

@section('content_header')
    <h1>مدیریت گزارش‌ها</h1>
@endsection

@section('content')

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif
    {{--
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
    </table> --}}

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>گزارش‌دهنده</th>
                <th>کاربر گزارش‌شده</th>
                <th>دلیل</th>
                <th>تاریخ</th>
                <th>وضعیت</th>
                <th>عملیات</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($reports as $report)
                <tr>
                    <td>{{ $report->reporter->name }}</td>
                    <td>{{ $report->reported->name }}</td>
                    <td>{{ $report->reason ?? '-' }}</td>
                    <td>{{ Jalalian::fromDateTime($report->created_at)->format('Y/m/d H:i') }}</td>
                    <td>
                        @if ($report->resolved)
                            <span class="badge bg-success">بررسی شده</span>
                        @else
                            <span class="badge bg-danger">بررسی نشده</span>
                        @endif
                    </td>
                    <td>
                        <form action="{{ route('admin.reports.resolve', $report->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-sm {{ $report->resolved ? 'btn-success' : 'btn-danger' }}">
                                {{ $report->resolved ? 'بررسی شد' : 'بررسی نشده' }}
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{ $reports->appends(request()->query())->links() }}

    <div class="d-flex justify-content-center mt-4">
        {!! $reports->links() !!}
    </div>

@endsection
