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
                        <th>نمایش</th>
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
                            <td>
                                <!-- دکمه نمایش -->
                                <button class="btn btn-sm btn-info" data-bs-toggle="modal"
                                    data-bs-target="#messageModal{{ $msg->id }}">
                                    نمایش
                                </button>

                                <!-- Modal -->
                                <div class="modal fade" id="messageModal{{ $msg->id }}" tabindex="-1"
                                    aria-labelledby="modalLabel{{ $msg->id }}" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-scrollable">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="modalLabel{{ $msg->id }}">پیام از
                                                    {{ $msg->sender->name }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="بستن"></button>
                                            </div>
                                            <div class="modal-body text-start">
                                                <p><strong>گیرنده:</strong> {{ $msg->receiver->name }}</p>
                                                <hr>
                                                <p>{{ $msg->body }}</p>
                                            </div>
                                            <div class="modal-footer">
                                                <button class="btn btn-secondary" data-bs-dismiss="modal">بستن</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
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
