@extends('adminlte::page')
@section('title', 'پیام‌های خصوصی')
@section('content_header')<h1>پیام‌های خصوصی کاربران</h1>@stop
@section('content')
    @if ($errors->any())
        <div class="alert alert-danger"><ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif
    @if (! $accessGranted)
        <div class="card card-warning">
            <div class="card-header"><h3 class="card-title">دسترسی محافظت‌شده</h3></div>
            <form method="POST" action="{{ route('admin.messages.access') }}">@csrf
                <div class="card-body">
                    <p>مشاهده محتوای خصوصی فقط برای رسیدگی ضروری مجاز است. دلیل و مشخصات دسترسی در Audit Log ثبت می‌شود و مجوز پس از ۱۵ دقیقه منقضی خواهد شد.</p>
                    <div class="form-group"><label for="reason">دلیل دسترسی</label><textarea id="reason" name="reason" class="form-control" minlength="10" maxlength="500" required>{{ old('reason') }}</textarea></div>
                    <div class="form-group"><label for="current_password">رمز عبور فعلی مدیر</label><input id="current_password" type="password" name="current_password" class="form-control" autocomplete="current-password" required></div>
                </div>
                <div class="card-footer"><button class="btn btn-warning" type="submit">ثبت دلیل و دریافت دسترسی</button></div>
            </form>
        </div>
    @else
        <div class="alert alert-warning d-flex justify-content-between align-items-center">
            <span><strong>دلیل ثبت‌شده:</strong> {{ $accessReason }}</span>
            <form method="POST" action="{{ route('admin.messages.access.revoke') }}">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-dark" type="submit">پایان دسترسی</button></form>
        </div>
        <form method="GET" class="card card-body mb-3"><div class="form-row">
            <div class="col-md-3"><input class="form-control" name="sender" value="{{ request('sender') }}" placeholder="فرستنده"></div>
            <div class="col-md-3"><input class="form-control" name="receiver" value="{{ request('receiver') }}" placeholder="گیرنده"></div>
            <div class="col-md-3"><input class="form-control" type="date" name="date" value="{{ request('date') }}"></div>
            <div class="col-md-3"><button class="btn btn-primary" type="submit">فیلتر</button></div>
        </div></form>
        <div class="card"><div class="card-body table-responsive p-0"><table class="table table-striped">
            <thead><tr><th>#</th><th>فرستنده</th><th>گیرنده</th><th>پیام</th><th>زمان</th></tr></thead><tbody>
            @forelse ($messages as $message)
                <tr><td>{{ $message->id }}</td><td>{{ $message->sender?->name ?? 'حذف‌شده' }}</td><td>{{ $message->receiver?->name ?? 'حذف‌شده' }}</td><td style="white-space:normal">{{ $message->message }}</td><td>{{ $message->created_at }}</td></tr>
            @empty <tr><td colspan="5" class="text-center">پیامی یافت نشد.</td></tr>
            @endforelse
            </tbody></table></div><div class="card-footer">{{ $messages->links() }}</div></div>
    @endif
@stop
