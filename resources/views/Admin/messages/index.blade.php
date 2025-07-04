@extends('adminlte::page')






@section('content')
    <div class="container mt-4">
        <h2 class="mb-4">لیست پیام‌ها</h2>
        <form method="GET" class="row g-3 mb-4">
            <div class="col-md-3">
                <input type="text" name="sender" value="{{ request('sender') }}" class="form-control"
                    placeholder="نام فرستنده">
            </div>
            <div class="col-md-3">
                <input type="text" name="receiver" value="{{ request('receiver') }}" class="form-control"
                    placeholder="نام گیرنده">
            </div>
            <div class="col-md-3">
                <input type="date" name="date" value="{{ request('date') }}" class="form-control">
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button class="btn btn-primary" type="submit">فیلتر</button>
                <a href="{{ route('admin.messages') }}" class="btn btn-secondary">پاک‌کردن</a>
            </div>
        </form>


        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-primary text-center">
                    <tr>
                        <th>#</th>
                        <th>فرستنده</th>
                        <th>گیرنده</th>
                        <th>متن پیام</th>
                        <th>تاریخ ارسال</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($messages as $msg)
                        <tr>
                            <td>{{ $msg->id }}</td>
                            <td>{{ $msg->sender->name ?? 'ناشناس' }}</td>
                            <td>{{ $msg->receiver->name ?? 'ناشناس' }}</td>
                            <td>{{ Str::limit($msg->message, 50) }}</td>
                            <td>{{ $msg->created_at->format('Y/m/d H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">هیچ پیامی یافت نشد</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center mt-3">
            {{ $messages->links() }}
        </div>
    </div>
@endsection
