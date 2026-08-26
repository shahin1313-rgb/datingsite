@extends('layouts.app')

@section('content')
    <div
        class="min-h-screen"
        x-data="{ openLogoutModal: false }"
    >
        @include('user.tickets.index')
    </div>

    <div
        class="fixed inset-0 bg-black bg-opacity-50 hidden md:hidden"
        id="myOverlay"
        data-toggle-sidebar
    ></div>
@endsection