@php
    use Morilog\Jalali\Jalalian;
@endphp

@extends('adminlte::page')

@section('title', 'مدیریت گزارش‌ها')

@section('content_header')
    <h1>مدیریت گزارش‌ها</h1>
@endsection

@section('content')
    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif

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
            @forelse ($reports as $report)
                <tr>
                    <td>
                        {{ $report->reporter->name ?? 'کاربر حذف‌شده' }}
                    </td>

                    <td>
                        {{ $report->reported->name ?? 'کاربر حذف‌شده' }}
                    </td>

                    <td>
                        {{ $report->reason ?? '-' }}
                    </td>

                    <td>
                        {{ Jalalian::fromDateTime($report->created_at)->format('Y/m/d H:i') }}
                    </td>

                    <td>
                        @if ($report->resolved)
                            <span class="badge bg-success">
                                بررسی شده
                            </span>
                        @else
                            <span class="badge bg-danger">
                                بررسی نشده
                            </span>
                        @endif
                    </td>

                    <td>
                        <form
                            action="{{ route('admin.reports.resolve', $report->id) }}"
                            method="POST"
                        >
                            @csrf

                            <button
                                type="submit"
                                class="btn btn-sm {{ $report->resolved ? 'btn-success' : 'btn-danger' }}"
                            >
                                {{ $report->resolved ? 'بررسی شد' : 'بررسی نشده' }}
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">
                        هیچ گزارشی ثبت نشده است.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="d-flex justify-content-center mt-4">
        {{ $reports->appends(request()->query())->links() }}
    </div>
@endsection