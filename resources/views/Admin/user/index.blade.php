@extends('adminlte::page')

@section('title', 'مدیریت کاربران')

@section('content_header')
    <h1>لیست کاربران</h1>
@endsection

@section('content')

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

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
