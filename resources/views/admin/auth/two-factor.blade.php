<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <meta
        name="robots"
        content="noindex, nofollow, noarchive"
    >

    <title>تأیید دومرحله‌ای مدیریت</title>

    <link
        rel="stylesheet"
        href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}"
    >

    <link
        rel="stylesheet"
        href="{{ asset('vendor/adminlte/dist/css/adminlte.min.css') }}"
    >

    <style>
        body {
            direction: rtl;
            min-height: 100vh;
            font-family: Tahoma, Arial, sans-serif;
            background:
                linear-gradient(
                    135deg,
                    #0f172a,
                    #111827 55%,
                    #020617
                );
        }

        .two-factor-box {
            width: 430px;
            max-width: calc(100% - 30px);
        }

        .two-factor-card {
            border: 0;
            border-radius: 20px;
            box-shadow:
                0 24px 70px rgba(0, 0, 0, 0.38);
        }

        .two-factor-card .card-body {
            padding: 32px;
        }

        .security-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 68px;
            height: 68px;
            margin: 0 auto 18px;
            border-radius: 20px;
            color: #ffffff;
            background:
                linear-gradient(
                    135deg,
                    #2563eb,
                    #1d4ed8
                );
            font-size: 28px;
        }

        .code-input {
            height: 58px;
            letter-spacing: 12px;
            font-size: 26px;
            font-weight: 700;
            text-align: center;
        }
    </style>
</head>

<body class="hold-transition login-page">

<main class="two-factor-box">
    <div class="card two-factor-card">
        <div class="card-body">

            <div class="security-icon">
                <i
                    class="fas fa-shield-alt"
                    aria-hidden="true"
                ></i>
            </div>

            <h1
                class="h4 text-center font-weight-bold mb-3"
            >
                تأیید دومرحله‌ای مدیریت
            </h1>

            <p class="text-muted text-center mb-4">
                کد ۶ رقمی ارسال‌شده به ایمیل مدیر را وارد کنید.
                کد ۵ دقیقه اعتبار دارد.
            </p>

            @if (session('status'))
                <div
                    class="alert alert-success text-right"
                >
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div
                    class="alert alert-danger text-right"
                >
                    {{ $errors->first() }}
                </div>
            @endif

            <form
                method="POST"
                action="{{ route('admin.two-factor.verify') }}"
            >
                @csrf

                <label
                    for="code"
                    class="d-block text-right mb-2"
                >
                    کد تأیید
                </label>

                <input
                    id="code"
                    name="code"
                    type="text"
                    inputmode="numeric"
                    pattern="[0-9]{6}"
                    maxlength="6"
                    autocomplete="one-time-code"
                    class="form-control code-input"
                    dir="ltr"
                    required
                    autofocus
                >

                <button
                    class="btn btn-primary btn-lg btn-block mt-3"
                    type="submit"
                >
                    تکمیل ورود
                </button>
            </form>

            <form
                method="POST"
                action="{{ route('admin.two-factor.cancel') }}"
            >
                @csrf

                <button
                    type="submit"
                    class="btn btn-link btn-block mt-2"
                >
                    بازگشت به ورود مدیریت
                </button>
            </form>

        </div>
    </div>
</main>

</body>
</html>