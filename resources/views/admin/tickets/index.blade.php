@extends('adminlte::page')
@section('title', 'تیکت‌ها')
@section('content_header')<h1>مدیریت تیکت‌ها</h1>@stop
@section('content')
    <form method="GET" class="card card-body"><div class="form-row">
        <div class="col-md-5"><input name="q" value="{{ request('q') }}" class="form-control" placeholder="موضوع، متن یا نام کاربر"></div>
        <div class="col-md-3"><select name="status" class="form-control"><option value="">همه وضعیت‌ها</option>@foreach(['open'=>'باز','answered'=>'پاسخ‌داده‌شده','closed'=>'بسته'] as $value=>$label)<option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>@endforeach</select></div>
        <div class="col-md-2"><button class="btn btn-primary" type="submit">جست‌وجو</button></div>
    </div></form>
    <div class="card"><div class="card-body table-responsive p-0"><table class="table table-hover">
        <thead><tr><th>#</th><th>کاربر</th><th>موضوع</th><th>وضعیت</th><th>پاسخ‌ها</th><th>تاریخ</th><th></th></tr></thead><tbody>
        @forelse($tickets as $ticket)
            <tr><td>{{ $ticket->id }}</td><td>{{ $ticket->user?->name ?? 'حذف‌شده' }}</td><td>{{ $ticket->subject }}</td><td>{{ ['open'=>'باز','answered'=>'پاسخ‌داده‌شده','closed'=>'بسته'][$ticket->status] ?? $ticket->status }}</td><td>{{ $ticket->replies_count }}</td><td>{{ $ticket->created_at }}</td><td><a class="btn btn-sm btn-outline-primary" href="{{ route('admin.tickets.show', $ticket) }}">جزئیات</a></td></tr>
        @empty <tr><td colspan="7" class="text-center">تیکتی یافت نشد.</td></tr>
        @endforelse
        </tbody></table></div><div class="card-footer">{{ $tickets->links() }}</div></div>
@stop
