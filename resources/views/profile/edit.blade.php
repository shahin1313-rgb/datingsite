

    @extends('layouts.app')

    @section('content')


    <div class="container">


    <div class="container py-5">
        <div class="row">
            <!-- Profile Header -->
            <div class="col-12 mb-4">
                <div class="profile-header position-relative mb-4">
                    <div class="position-absolute top-0 end-0 p-3">
                    </div>
                </div>
                <div class="text-center">
                    <div class="position-relative d-inline-block">


                        <img src="{{asset('storage/' . $user->profile_picture)}}" class="rounded-circle profile-pic" alt="Profile Picture">
                        <button class="btn btn-primary btn-sm position-absolute bottom-0 end-0 rounded-circle">
                            <i class="fas fa-camera"></i>
                        </button>
                    </div>
                    <h3 class="mt-3 mb-1">{{ $user->name }}</h3>
                    <p class="text-muted mb-3">Senior Product Designer</p>
                    <div class="d-flex justify-content-center gap-2 mb-4">
                        <button class="btn btn-outline-primary"><i class="fas fa-envelope me-2"></i>pic update</button>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-0">
                        <div class="row g-0 justify-content-center">


                            <!-- Content Area -->

                            <div class="col-lg-9">
                                <div class="p-4   " >
                                    <!-- Personal Information -->
                                    {{-- <div class="mb-4">
                                        <h5 class="mb-4">Personal Information</h5>
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label">First Name</label>
                                                <input type="text" class="form-control" value="Alex">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Last Name</label>
                                                <input type="text" class="form-control" value="Johnson">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Email</label>
                                                <input type="email" class="form-control" value="testnson@example.com">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Phone</label>
                                                <input type="tel" class="form-control" value="+1 (555) 123-4567">
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label">Bio</label>
                                                <textarea class="form-control" rows="4">Product designer with 5+ years of experience in creating user-centered digital solutions. Passionate about solving complex problems through simple and elegant designs.</textarea>
                                            </div>
                                        </div>
                                    </div> --}}
                                    <div class="container mt-5">
                                        <h1>Edit Profile</h1>
                                        <form action="{{ route('profile.update') }}" method="POST">
                                            @csrf
                                            <div class="form-group mb-3">
                                                <label for="name">Name</label>
                                                <input type="text" name="name" class="form-control" value="{{ $user->name }}" required>
                                            </div>
                                            <div class="form-group mb-3">
                                                <label for="email">Email</label>
                                                <input type="email" name="email" class="form-control" value="{{ $user->email }}" required>
                                            </div>
                                            <div class="form-group mb-3">
                                                <label for="city">City</label>
                                                <input type="text" name="city" class="form-control" value="{{ $user->city }}">
                                            </div>
                                            <div class="form-group mb-3">
                                                <label for="bio">Bio</label>
                                                <textarea name="bio" class="form-control" rows="4">{{ $user->bio }}</textarea>
                                            </div>
                                            <button type="submit" class="btn btn-primary">Update Profile</button>
                                        </form>
                                    </div>


                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


</div>

@endsection
