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

    <title>ورود به پنل مدیریت</title>

    <link
        rel="stylesheet"
        href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}"
    >

    <link
        rel="stylesheet"
        href="{{ asset('vendor/adminlte/dist/css/adminlte.min.css') }}"
    >

    <style>
        body.admin-login-page {
            direction: rtl;
            min-height: 100vh;
            font-family: Tahoma, Arial, sans-serif;
            background:
                radial-gradient(
                    circle at top right,
                    rgba(59, 130, 246, 0.22),
                    transparent 35%
                ),
                linear-gradient(
                    135deg,
                    #0f172a,
                    #111827 55%,
                    #020617
                );
        }

        .admin-login-box {
            width: 410px;
            max-width: calc(100% - 30px);
        }

        .admin-brand {
            margin-bottom: 20px;
            color: #ffffff;
            text-align: center;
        }

        .admin-brand-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 76px;
            height: 76px;
            margin-bottom: 14px;
            border: 1px solid rgba(255, 255, 255, 0.18);
            border-radius: 22px;
            color: #ffffff;
            background: rgba(255, 255, 255, 0.10);
            box-shadow: 0 18px 50px rgba(0, 0, 0, 0.28);
            backdrop-filter: blur(10px);
        }

        .admin-brand-icon i {
            font-size: 32px;
        }

        .admin-brand h1 {
            margin: 0;
            font-size: 25px;
            font-weight: 700;
        }

        .admin-brand p {
            margin-top: 8px;
            color: #cbd5e1;
            font-size: 13px;
        }

        .admin-login-card {
            overflow: hidden;
            border: 0;
            border-radius: 20px;
            box-shadow: 0 24px 70px rgba(0, 0, 0, 0.38);
        }

        .admin-login-card .card-body {
            padding: 30px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            color: #334155;
            font-size: 13px;
            font-weight: 700;
            text-align: right;
        }

        .input-group {
            direction: ltr;
        }

        .form-control {
            height: 48px;
        }

        .input-group-text,
        .password-toggle {
            width: 48px;
            justify-content: center;
            border: 1px solid #ced4da;
            color: #64748b;
            background: #f8fafc;
        }

        .password-toggle {
            cursor: pointer;
        }

        .password-toggle:focus {
            outline: 2px solid #93c5fd;
            outline-offset: -2px;
        }

        .login-button {
            height: 48px;
            border: 0;
            border-radius: 10px;
            font-weight: 700;
            background: linear-gradient(
                135deg,
                #2563eb,
                #1d4ed8
            );
            box-shadow: 0 10px 24px rgba(37, 99, 235, 0.25);
        }

        .security-note {
            margin-top: 20px;
            padding: 12px;
            border-radius: 10px;
            color: #475569;
            background: #f8fafc;
            font-size: 12px;
            line-height: 1.9;
        }

        .back-link {
            display: block;
            margin-top: 18px;
            color: #cbd5e1;
            font-size: 13px;
            text-align: center;
        }

        .back-link:hover {
            color: #ffffff;
        }

        .invalid-feedback {
            direction: rtl;
            text-align: right;
        }
    </style>
</head>

<body class="hold-transition login-page admin-login-page">

<div class="admin-login-box">

    <div class="admin-brand">
        <div class="admin-brand-icon">
            <i
                class="fas fa-user-shield"
                aria-hidden="true"
            ></i>
        </div>

        <h1>پنل مدیریت</h1>

        <p>ورود فقط برای مدیران مجاز سایت</p>
    </div>

    <div class="card admin-login-card">
        <div class="card-body">

            @if ($errors->any())
                <div
                    class="alert alert-danger text-right"
                    role="alert"
                >
                    <i
                        class="fas fa-exclamation-triangle ml-1"
                        aria-hidden="true"
                    ></i>

                    {{ $errors->first() }}
                </div>
            @endif

            <form
                method="POST"
                action="{{ route('admin.login.submit') }}"
                autocomplete="on"
            >
                @csrf

                <div class="form-group">
                    <label
                        class="form-label"
                        for="email"
                    >
                        ایمیل مدیر
                    </label>

                    <div class="input-group">
                        <input
                            id="email"
                            type="email"
                            name="email"
                            class="form-control @error('email') is-invalid @enderror"
                            value="{{ old('email') }}"
                            placeholder="admin@example.com"
                            autocomplete="username"
                            maxlength="255"
                            required
                            autofocus
                        >

                        <div class="input-group-append">
                            <span class="input-group-text">
                                <i
                                    class="fas fa-envelope"
                                    aria-hidden="true"
                                ></i>
                            </span>
                        </div>
                    </div>

                    @error('email')
                        <span class="invalid-feedback d-block">
                            {{ $message }}
                        </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label
                        class="form-label"
                        for="password"
                    >
                        رمز عبور
                    </label>

                    <div class="input-group">
                        <input
                            id="password"
                            type="password"
                            name="password"
                            class="form-control @error('password') is-invalid @enderror"
                            placeholder="رمز عبور"
                            autocomplete="current-password"
                            maxlength="255"
                            required
                        >

                        <div class="input-group-append">
                            <button
                                id="password-toggle"
                                class="input-group-text password-toggle"
                                type="button"
                                aria-label="نمایش رمز عبور"
                                aria-pressed="false"
                            >
                                <i
                                    id="password-icon"
                                    class="fas fa-eye"
                                    aria-hidden="true"
                                ></i>
                            </button>
                        </div>
                    </div>

                    @error('password')
                        <span class="invalid-feedback d-block">
                            {{ $message }}
                        </span>
                    @enderror
                </div>

                <div class="form-group text-right">
                    <div class="custom-control custom-checkbox">
                        <input
                            id="remember"
                            type="checkbox"
                            name="remember"
                            value="1"
                            class="custom-control-input"
                            @checked(old('remember'))
                        >

                        <label
                            class="custom-control-label"
                            for="remember"
                        >
                            مرا به خاطر بسپار
                        </label>
                    </div>
                </div>

                <button
                    type="submit"
                    class="btn btn-primary btn-block login-button"
                >
                    <i
                        class="fas fa-sign-in-alt ml-1"
                        aria-hidden="true"
                    ></i>

                    ورود به مدیریت
                </button>
            </form>

            <div class="security-note">
                <i
                    class="fas fa-lock ml-1"
                    aria-hidden="true"
                ></i>

                تلاش‌های ناموفق ورود ثبت و محدود می‌شوند.
                دسترسی غیرمجاز به پنل مدیریت ممنوع است.
            </div>
        </div>
    </div>

    <a class="back-link" href="{{ url('/') }}">
        <i
            class="fas fa-arrow-right ml-1"
            aria-hidden="true"
        ></i>

        بازگشت به سایت
    </a>
</div>

<script nonce="{{ \Illuminate\Support\Facades\Vite::cspNonce() }}">
    document.addEventListener('DOMContentLoaded', () => {
        const passwordInput =
            document.getElementById('password');

        const toggleButton =
            document.getElementById('password-toggle');

        const passwordIcon =
            document.getElementById('password-icon');

        toggleButton?.addEventListener('click', () => {
            const passwordIsHidden =
                passwordInput.type === 'password';

            passwordInput.type =
                passwordIsHidden ? 'text' : 'password';

            passwordIcon.classList.toggle(
                'fa-eye',
                !passwordIsHidden
            );

            passwordIcon.classList.toggle(
                'fa-eye-slash',
                passwordIsHidden
            );

            toggleButton.setAttribute(
                'aria-pressed',
                passwordIsHidden ? 'true' : 'false'
            );

            toggleButton.setAttribute(
                'aria-label',
                passwordIsHidden
                    ? 'مخفی‌کردن رمز عبور'
                    : 'نمایش رمز عبور'
            );
        });
    });
</script>

</body>
</html>