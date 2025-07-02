@extends('layouts.admin')

@section('content')
<div class="container">
    <h2>Admin Dashboard</h2>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
            <tr>
                <td>{{ $user->id }}</td>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>{{ $user->role }}</td>
                <td>
                    @if($user->role !== 'admin')
                        <form action="{{ route('admin.makeAdmin', $user->id) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-primary">Make Admin</button>
                        </form>
                    @endif
                </td>
                <td>
                    <form action="{{ route('admin.toggleBan', $user->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn {{ $user->banned ? 'btn-success' : 'btn-danger' }}">
                            {{ $user->banned ? 'Unban' : 'Ban' }}
                        </button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    {{ $users->links() }}
</div>
@endsection
