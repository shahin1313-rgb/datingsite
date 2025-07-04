@extends('adminlte::page')

@section('title', 'مدیریت کاربران')

@section('content_header')
    <h1>لیست کاربران</h1>
@endsection

@section('content')

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif
    <form method="GET" action="{{ route('admin.users') }}" class="mb-4">
        <div class="row">
            <div class="col-md-3">
                <input type="text" name="name" class="form-control" placeholder="نام" value="{{ request('name') }}">
            </div>
            <div class="col-md-3">
                <input type="text" name="email" class="form-control" placeholder="ایمیل"
                    value="{{ request('email') }}">
            </div>
            <div class="col-md-3">
                <select name="banned" class="form-control">
                    <option value="">-- وضعیت --</option>
                    <option value="1" {{ request('banned') == '1' ? 'selected' : '' }}>بن شده</option>
                    <option value="0" {{ request('banned') == '0' ? 'selected' : '' }}>فعال</option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary">جستجو</button>
                <a href="{{ route('admin.users') }}" class="btn btn-secondary">ریست</a>
            </div>
        </div>
    </form>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>شناسه</th>
                <th>نام</th>
                <th>ایمیل</th>
                <th>وضعیت</th>
                <th>عملیات</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($users as $user)
                <tr @if ($user->banned) class="table-danger" @endif>
                    <td>{{ $user->id }}</td>
                    <td>
                        <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-info btn-sm">مشاهده پروفایل</a>
                    </td>

                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->banned ? 'بن شده' : 'فعال' }}</td>
                    <td>
                        <form action="{{ route('admin.users.ban', $user) }}" method="POST" style="display:inline;">
                            @csrf
                            <button class="btn btn-sm btn-warning" onclick="return confirm('آیا مطمئنی؟')">
                                {{ $user->banned ? 'آزاد کن' : 'بن کن' }}
                            </button>
                        </form>

                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger" onclick="return confirm('حذف شود؟')">حذف</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{ $users->links('pagination::bootstrap-5') }}
@endsection
