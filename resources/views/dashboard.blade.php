@extends('layouts.app')

@section('content')
    <div class="container">

        {{-- 1️⃣ Sidebar --}}
        <nav class="w3-sidebar w3-collapse w3-white w3-animate-left" style="z-index:3;width:300px;" id="mySidebar"><br>
            <div class="w3-container w3-row">
                <div class="w3-col s4">
                    <img src="{{ asset('storage/' . $user->profile_picture) }}" class="w3-circle w3-margin-right"
                        style="width:46px">
                </div>
                <div class="w3-col s8 w3-bar">
                    <span>سلام، {{ $user->name }}</span><br>
                    <a href="#" class="w3-bar-item w3-button"><i class="fa fa-envelope"></i></a>
                    <a href="#" class="w3-bar-item w3-button"><i class="fa fa-user"></i></a>
                    <a href="#" class="w3-bar-item w3-button"><i class="fa fa-cog"></i></a>
                </div>
            </div>
            <hr>
            <div class="w3-container">
                <h5>داشبورد</h5>
            </div>
            <div class="w3-bar-block">
                <a href="#" class="w3-bar-item w3-button w3-blue"><i class="fa fa-users fa-fw"></i> نمای کلی</a>
                <a href="{{ route('profile.show', Auth::id()) }}" class="w3-bar-item w3-button"><i class="fa fa-eye"></i>
                    پروفایل</a>
                <a href="{{ route('search') }}" class="w3-bar-item w3-button"><i class="fa fa-search"></i> جستجو</a>
                <a href="{{ route('profile.edit') }}" class="w3-bar-item w3-button"><i class="fa fa-edit"></i> ویرایش
                    پروفایل</a>
                <a href="#" class="w3-bar-item w3-button"><i class="fa fa-diamond"></i> خرید</a>
                <a href="#" class="w3-bar-item w3-button"><i class="fa fa-bell"></i> پشتیبانی</a>
                <a href="#" class="w3-bar-item w3-button"><i class="fa fa-shield"></i> پلیس سایت</a>
                <a href="#" class="w3-bar-item w3-button"><i class="fa fa-history"></i> تاریخچه</a>
                <a href="#" class="w3-bar-item w3-button"><i class="fa fa-cog"></i> تنظیمات</a>
            </div>
        </nav>

        {{-- 2️⃣ Main Content --}}
        <div class="w3-main" style="margin-left:300px;margin-top:43px;">

            {{-- Header --}}
            <header class="w3-container" style="padding-top:22px">
                <h5><b><i class="fa fa-dashboard"></i> داشبورد من</b></h5>
            </header>

            {{-- Info Boxes --}}
            <div class="w3-row-padding w3-margin-bottom">
                <div class="w3-quarter">
                    <a href="{{ route('messages.index') }}">
                        <div class="w3-container w3-red w3-padding-16">
                            <div class="w3-left"><i class="fa fa-comment w3-xxxlarge"></i></div>
                            <div class="w3-right">
                                <h3>{{ Auth::user()->unreadMessagesCount() }}</h3>
                            </div>
                            <div class="w3-clear"></div>
                            <h4>پیام جدید</h4>
                        </div>
                    </a>
                </div>

                <div class="w3-quarter">
                    <a href="{{ route('profile.edit') }}">
                        <div class="w3-container w3-blue w3-padding-16">
                            <div class="w3-left"><i class="fa fa-user w3-xxxlarge"></i></div>
                            <div class="w3-right">
                                <h3>ویرایش</h3>
                            </div>
                            <div class="w3-clear"></div>
                            <h4>پروفایل</h4>
                        </div>
                    </a>
                </div>

                <div class="w3-quarter">
                    <div class="w3-container w3-teal w3-padding-16">
                        <div class="w3-left"><i class="fa fa-credit-card w3-xxxlarge"></i></div>
                        <div class="w3-right">
                            <h3>23</h3>
                        </div>
                        <div class="w3-clear"></div>
                        <h4>پرداخت شارژ</h4>
                    </div>
                </div>

                <div class="w3-quarter">
                    <div class="w3-container w3-orange w3-text-white w3-padding-16">
                        <div class="w3-left"><i class="fa fa-heart w3-xxxlarge"></i></div>
                        <div class="w3-right">
                            <h3>50</h3>
                        </div>
                        <div class="w3-clear"></div>
                        <h4>افراد مچ‌شده</h4>
                    </div>
                </div>
            </div>

            {{-- جدول و اطلاعیه‌ها --}}
            <div class="w3-panel">
                <div class="w3-row-padding">
                    <div class="w3-third">
                        <h5>مناطق</h5>
                    </div>
                    <div class="w3-twothird">
                        <h5>فعالیت‌ها</h5>
                        <table class="w3-table w3-striped w3-white">
                            {{-- نمونه ردیف‌ها --}}
                            <tr>
                                <td><i class="fa fa-user w3-text-blue"></i></td>
                                <td>مشاهده جدید</td>
                                <td>10 دقیقه قبل</td>
                            </tr>
                            <tr>
                                <td><i class="fa fa-bell w3-text-red"></i></td>
                                <td>هشدار سیستم</td>
                                <td>15 دقیقه قبل</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            {{-- جدول کشورها --}}
            <div class="w3-container">
                <h5>کشورها</h5>
                <table class="w3-table w3-striped w3-bordered w3-white">
                    <tr>
                        <td>آمریکا</td>
                        <td>65%</td>
                    </tr>
                    <tr>
                        <td>انگلستان</td>
                        <td>15%</td>
                    </tr>
                </table>
            </div>

            {{-- 3️⃣ بخش فرعی: Carousel، نظرات، آمار --}}
            <div class="w3-container mt-4">
                <h4>اعضای فعال</h4>
                <section class="customer-logos slider">
                    @foreach ($recentUsers as $recentUser)
                        <div class="slide">
                            <img src="{{ asset('storage/' . $recentUser->profile_picture) }}">
                        </div>
                    @endforeach
                </section>
            </div>

            <div class="w3-container mt-4">
                <h5>نظرات اخیر</h5>
                <p>John: کار عالی!</p>
                <p>Ali: منتظرم برای آپدیت بعدی</p>
            </div>

            {{-- آمار پایینی --}}
            <div class="w3-container w3-dark-grey w3-padding-32">
                <div class="w3-row">
                    <div class="w3-third">
                        <h5>جمعیت</h5>
                        <p>کشور / شهر</p>
                    </div>
                    <div class="w3-third">
                        <h5>سیستم</h5>
                        <p>مرورگر / سیستم‌عامل</p>
                    </div>
                    <div class="w3-third">
                        <h5>هدف</h5>
                        <p>علایق کاربران</p>
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <footer class="w3-container w3-light-grey w3-padding-16">
                <p>ساخته شده با W3.CSS و Laravel</p>
            </footer>

        </div>

    </div>

    {{-- Scripts --}}
    <link rel="stylesheet" type="text/css"
        href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.6.0/slick.css" />
    <link rel="stylesheet" href="{{ asset('css/styles3.css') }}">
    <script src="https://code.jquery.com/jquery-2.2.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.6.0/slick.js"></script>
    <script src="{{ asset('js/slick-carousel.js') }}"></script>

    <script>
        function w3_open() {
            document.getElementById("mySidebar").style.display = 'block';
            document.getElementById("myOverlay").style.display = 'block';
        }

        function w3_close() {
            document.getElementById("mySidebar").style.display = 'none';
            document.getElementById("myOverlay").style.display = 'none';
        }
    </script>
@endsection
