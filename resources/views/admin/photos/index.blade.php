@extends('adminlte::page')

@section('content')
    <div class="container">
        <h2 class="mb-4">تصاویر کاربران</h2>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>کاربر</th>
                    <th>ایمیل</th>
                    <th>تصویر</th>
                    <th>عملیات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            <img src="{{ asset('storage/' . $user->profile_picture) }}" alt="Profile" width="80"
                                height="80" class="rounded">
                        </td>
                        <td>
                            <form action="{{ route('admin.photos.destroy', $user->id) }}" method="POST"
                                onsubmit="return confirm('آیا مطمئن هستید؟');">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger btn-sm">حذف تصویر</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4">هیچ تصویری یافت نشد.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
