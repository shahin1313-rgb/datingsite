@extends('layouts.app')

@section('content')
 
    <div class="min-h-screen" x-data="{ openLogoutModal: false }">
        
         

            <!-- محتوای اصلی -->
            @include('user.tickets.index')


        </div>

       

    <div class="fixed inset-0 bg-black bg-opacity-50 hidden md:hidden" id="myOverlay" onclick="toggleSidebar()"></div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.6.0/slick.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
   
@endsection
