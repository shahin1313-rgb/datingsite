

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Our Dating Site</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    @section('head')
    <!-- Include your custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/styles4.css') }}">
@endsection

</head>
<body>
    <div class="container text-center mt-5">
        <h1>Welcome to Our Dating Site</h1>
        <p>Find your perfect match today!</p>
        {{-- <img class="rounded-circle shadow-4-strong card-img-top" alt="avatar2" src="{{asset('storage/' . $profile->profile_picture)}}" /> --}}

        <div class="mt-4">
            <a href="{{ route('login') }}" class="btn btn-primary">Login</a>
            <a href="{{ route('register') }}" class="btn btn-success">Register</a>
        </div>
    </div>
</body>
</html>
