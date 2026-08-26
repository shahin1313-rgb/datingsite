<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>صفحه پیدا نشد</title>

    @vite('resources/css/app.css')
</head>

<body
    class="bg-gradient-to-br from-pink-50 via-white to-purple-50 h-screen flex items-center justify-center"
>
    <div class="text-center px-6">
        <h1 class="text-9xl font-extrabold text-pink-500">
            404
        </h1>

        <p
            class="text-2xl md:text-3xl font-semibold text-gray-800 mt-4"
        >
            اوه! صفحه مورد نظر پیدا نشد 💔
        </p>

        <p class="text-gray-500 mt-2">
            شاید لینک اشتباه است یا صفحه حذف شده.
        </p>

        <div class="mt-8">
            <a
                href="{{ url('/') }}"
                class="px-6 py-3 rounded-full bg-pink-500 text-white font-medium shadow-lg hover:bg-pink-600 transition duration-300"
            >
                بازگشت به خانه 🏠
            </a>
        </div>
    </div>
</body>

</html>