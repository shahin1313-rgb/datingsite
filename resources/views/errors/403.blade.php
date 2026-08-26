<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>403 - دسترسی ممنوع</title>

    @vite('resources/css/app.css')
</head>

<body
    class="bg-gray-100 flex items-center justify-center min-h-screen"
>
    <div class="text-center px-6">
        <h1 class="text-9xl font-extrabold text-red-600">
            403
        </h1>

        <p class="text-2xl md:text-3xl font-bold text-gray-800 mt-4">
            دسترسی شما به این صفحه ممنوع است
        </p>

        <p class="text-gray-500 mt-2">
            ممکن است مجوز لازم برای مشاهده این بخش را نداشته باشید.
        </p>

        <div class="mt-6">
            <a
                href="{{ url('/') }}"
                class="px-6 py-3 bg-red-600 text-white rounded-lg shadow hover:bg-red-700 transition"
            >
                بازگشت به صفحه اصلی
            </a>
        </div>
    </div>
</body>

</html>