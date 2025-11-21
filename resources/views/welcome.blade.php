<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لندینگ پیج سایت دوستیابی</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Vazir:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Vazir', sans-serif;
        }

        .hero-bg {
            background: linear-gradient(135deg, #ff5e62 0%, #f6d365 100%);
            background-size: cover;
            position: relative;
            overflow: hidden;
        }

        .hero-bg::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('https://source.unsplash.com/random/1920x1080/?love') no-repeat center center/cover;
            opacity: 0.2;
            z-index: 0;
        }

        .hero-content {
            position: relative;
            z-index: 1;
        }

        .btn-primary {
            transition: transform 0.3s ease, background-color 0.3s ease;
        }

        .btn-primary:hover {
            transform: scale(1.05);
            background-color: #fefcbf;
        }

        .feature-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<body class="bg-gray-50">
    <!-- هدر -->
    <header class="bg-white shadow-lg sticky top-0 z-50">
        <nav class="container mx-auto px-6 py-4 flex justify-between items-center">
            <div class="text-3xl font-bold text-pink-600">عشق‌یاب</div>
            <div class="space-x-6 space-x-reverse">
                <a href="{{ route('login') }}" class="text-gray-700 hover:text-pink-500 transition-colors">ورود</a>
                <a href="{{ route('register') }}"
                    class="bg-pink-500 text-white px-5 py-2 rounded-full font-semibold hover:bg-pink-600 transition-colors">ثبت‌نام</a>
            </div>
        </nav>
    </header>

    <!-- هیرو سکشن -->
    <section class="hero-bg text-white py-24 md:py-32">
        <div class="container mx-auto px-6 text-center hero-content">
            <h1 class="text-4xl md:text-6xl font-bold mb-6 animate-fade-in-down">عشق زندگی‌ات را همین‌جا پیدا کن</h1>
            <p class="text-lg md:text-2xl mb-8 max-w-2xl mx-auto">با عشق‌یاب، به دنیای ارتباطات عاشقانه قدم بگذارید و با
                افرادی خاص ملاقات کنید.</p>
            <a href="{{ route('register') }}"
                class="btn-primary bg-white text-pink-600 px-8 py-4 rounded-full text-lg font-semibold shadow-lg">همین
                حالا شروع کن</a>
        </div>
    </section>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-10">

    <!-- Total Messages -->
    <div class="text-center bg-white shadow p-6 rounded-lg">
        <h3 class="text-xl font-bold text-gray-700">تعداد کل پیام‌ها</h3>
        <span class="text-4xl font-extrabold text-blue-600 counter" data-target="{{ $totalMessages }}">0</span>
    </div>

    <!-- Messages This Month -->
    <div class="text-center bg-white shadow p-6 rounded-lg">
        <h3 class="text-xl font-bold text-gray-700">پیام‌های این ماه</h3>
        <span class="text-4xl font-extrabold text-green-600 counter" data-target="{{ $monthlyMessages }}">0</span>
    </div>

    <!-- Messages Today -->
    <div class="text-center bg-white shadow p-6 rounded-lg">
        <h3 class="text-xl font-bold text-gray-700">پیام‌های امروز</h3>
        <span class="text-4xl font-extrabold text-red-600 counter" data-target="{{ $todayMessages }}">0</span>
    </div>

</div>


    <!-- ویژگی‌ها -->
    <section class="py-20 bg-white">
        <div class="container mx-auto px-6">
            <h2 class="text-4xl font-bold text-center text-gray-800 mb-12">چرا عشق‌یاب را انتخاب کنید؟</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="feature-card bg-gray-50 p-6 rounded-lg text-center">
                    <div class="text-5xl mb-4">💖</div>
                    <h3 class="text-2xl font-semibold text-gray-800 mb-3">مطابقت هوشمند</h3>
                    <p class="text-gray-600">الگوریتم‌های پیشرفته ما بهترین شریک را برای شما پیدا می‌کنند.</p>
                </div>
                <div class="feature-card bg-gray-50 p-6 rounded-lg text-center">
                    <div class="text-5xl mb-4">🔒</div>
                    <h3 class="text-2xl font-semibold text-gray-800 mb-3">امنیت بی‌نظیر</h3>
                    <p class="text-gray-600">حریم خصوصی شما با بالاترین استانداردها محافظت می‌شود.</p>
                </div>
                <div class="feature-card bg-gray-50 p-6 rounded-lg text-center">
                    <div class="text-5xl mb-4">💬</div>
                    <h3 class="text-2xl font-semibold text-gray-800 mb-3">گفت‌وگوی آسان</h3>
                    <p class="text-gray-600">با رابط کاربری جذاب، به راحتی ارتباط برقرار کنید.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- فراخوان به اقدام -->
    <section class="py-20 bg-gradient-to-r from-pink-500 to-red-500 text-white">
        <div class="container mx-auto px-6 text-center">
            <h2 class="text-4xl font-bold mb-6">عشق در انتظار شماست!</h2>
            <p class="text-xl mb-8 max-w-xl mx-auto">امروز به عشق‌یاب بپیوندید و داستان عاشقانه خود را بنویسید.</p>
            <a href="{{ route('register') }}"
                class="btn-primary bg-white text-pink-600 px-8 py-4 rounded-full text-lg font-semibold shadow-lg">ثبت‌نام
                رایگان</a>
        </div>
    </section>

    <!-- فوتر -->
    <footer class="bg-gray-900 text-white py-10">
        <div class="container mx-auto px-6 text-center">
            <p class="mb-6 text-gray-400">© 2025 عشق‌یاب. تمامی حقوق محفوظ است.</p>
            <div class="space-x-6 space-x-reverse">
                <a href="#" class="text-gray-400 hover:text-pink-400 transition-colors">درباره ما</a>
                <a href="#" class="text-gray-400 hover:text-pink-400 transition-colors">تماس با ما</a>
                <a href="#" class="text-gray-400 hover:text-pink-400 transition-colors">سیاست حریم خصوصی</a>
            </div>
        </div>
    </footer>

    <script>

        
    document.addEventListener("DOMContentLoaded", () => {
        const counters = document.querySelectorAll(".counter");

        counters.forEach(counter => {
            const target = +counter.getAttribute("data-target");
            let count = 0;

            // سرعت شمارش
            const speed = target / 50;

            function updateCounter() {
                if (count < target) {
                    count += speed;
                    counter.textContent = Math.ceil(count);
                    requestAnimationFrame(updateCounter);
                } else {
                    counter.textContent = target; // تثبیت عدد نهایی
                }
            }

            updateCounter();
        });
    });


        // انیمیشن ساده برای fade-in
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.animate-fade-in-down').forEach(el => {
                el.style.opacity = 0;
                el.style.transform = 'translateY(-20px)';
                setTimeout(() => {
                    el.style.transition = 'opacity 1s ease, transform 1s ease';
                    el.style.opacity = 1;
                    el.style.transform = 'translateY(0)';
                }, 100);
            });
        });
    </script>
</body>

</html>
