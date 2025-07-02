<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <nav>

            <a href="{{ route('admin.dashboard') }}">Dashboard</a>
            <a href="{{ route('admin.reports') }}">Reports</a>
            {{-- <a href="{{ route('admin.settings') }}">Settings</a> --}}
            <a href="{{ route('logout') }}">Logout</a>

    </nav>
    <div class="content">
        @yield('content')
    </div>
</body>
</html>
