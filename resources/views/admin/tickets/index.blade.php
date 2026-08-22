<!-- resources/views/errors/404.blade.php -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Page Not Found</title>
    @vite('resources/css/app.css')
</head>

<body class="h-screen flex items-center justify-center bg-gradient-to-br from-pink-100 via-purple-100 to-pink-200">

    <div class="text-center p-8 bg-white/70 backdrop-blur-sm rounded-2xl shadow-lg max-w-lg w-full">

        <!-- قلب انیمیشنی -->
        <div class="flex justify-center mb-6">
            <svg class="w-20 h-20 text-pink-500 animate-pulse" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                viewBox="0 0 24 24">
                <path
                    d="M12 21s-6-4.35-9-8.7C.45 9.45 2.7 3 8.1 3c2.25 0 3.9 1.2 3.9 3 0-1.8 1.65-3 3.9-3 5.4 0 7.65 6.45 5.1 9.3-3 4.35-9 8.7-9 8.7z" />
            </svg>
        </div>

        <!-- متن 404 -->
        <h1 class="text-6xl font-extrabold text-gray-800 mb-4">404</h1>
        <p class="text-gray-600 text-lg mb-6">اوه! صفحه‌ای که دنبالش هستید پیدا نشد 💔</p>

        <!-- دکمه -->
        <a href="{{ url('/') }}"
            class="inline-block px-6 py-3 bg-pink-500 hover:bg-pink-600 text-white font-semibold rounded-full shadow-md transition">
            بازگشت به خانه
        </a>
    </div>

</body>

</html>
