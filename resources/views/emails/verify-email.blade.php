<!DOCTYPE html>
<html
    lang="fa"
    dir="rtl"
>
<head>
    <meta charset="utf-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <meta
        http-equiv="x-ua-compatible"
        content="ie=edge"
    >

    <title>
        تأیید ایمیل ولورا
    </title>

    <style>
        @media only screen and (max-width: 620px) {
            .email-container {
                width: 100% !important;
            }

            .email-content {
                padding: 28px 20px !important;
            }

            .email-title {
                font-size: 24px !important;
            }

            .verify-button {
                display: block !important;
                width: auto !important;
            }
        }
    </style>
</head>

<body
    style="
        margin: 0;
        padding: 0;
        width: 100%;
        background-color: #fff1f5;
        font-family: Tahoma, Arial, sans-serif;
        direction: rtl;
        color: #1f2937;
    "
>
    {{-- متن مخفی پیش‌نمایش Gmail --}}
    <div
        style="
            display: none;
            max-height: 0;
            overflow: hidden;
            opacity: 0;
            color: transparent;
        "
    >
        برای فعال‌سازی حساب ولورا، ایمیل خود را تأیید کنید.
    </div>

    <table
        role="presentation"
        cellpadding="0"
        cellspacing="0"
        border="0"
        width="100%"
        style="
            width: 100%;
            background-color: #fff1f5;
        "
    >
        <tr>
            <td
                align="center"
                style="padding: 36px 12px;"
            >
                <table
                    role="presentation"
                    cellpadding="0"
                    cellspacing="0"
                    border="0"
                    width="600"
                    class="email-container"
                    style="
                        width: 600px;
                        max-width: 600px;
                        background-color: #ffffff;
                        border-radius: 24px;
                        overflow: hidden;
                        box-shadow: 0 12px 40px rgba(236, 72, 153, 0.14);
                        border: 1px solid #fce7f3;
                    "
                >
                    {{-- سربرگ --}}
                    <tr>
                        <td
                            align="center"
                            style="
                                padding: 32px 24px;
                                background-color: #ec4899;
                                background-image: linear-gradient(
                                    135deg,
                                    #ec4899,
                                    #f43f5e
                                );
                            "
                        >
                            <div
                                style="
                                    display: inline-block;
                                    width: 66px;
                                    height: 66px;
                                    line-height: 66px;
                                    border-radius: 50%;
                                    background-color: rgba(255, 255, 255, 0.20);
                                    color: #ffffff;
                                    font-size: 32px;
                                    text-align: center;
                                    margin-bottom: 12px;
                                "
                            >
                                ♡
                            </div>

                            <div
                                style="
                                    color: #ffffff;
                                    font-size: 28px;
                                    line-height: 36px;
                                    font-weight: 900;
                                    letter-spacing: -1px;
                                "
                            >
                                vlora
                            </div>

                            <div
                                style="
                                    margin-top: 6px;
                                    color: #ffe4e6;
                                    font-size: 13px;
                                    line-height: 22px;
                                "
                            >
                                ارتباطی امن، واقعی و صمیمی
                            </div>
                        </td>
                    </tr>

                    {{-- محتوای ایمیل --}}
                    <tr>
                        <td
                            class="email-content"
                            style="padding: 42px 46px;"
                        >
                            <div
                                style="
                                    text-align: center;
                                    margin-bottom: 28px;
                                "
                            >
                                <div
                                    style="
                                        display: inline-block;
                                        padding: 7px 14px;
                                        border-radius: 999px;
                                        background-color: #fdf2f8;
                                        color: #db2777;
                                        font-size: 12px;
                                        font-weight: 700;
                                        margin-bottom: 16px;
                                    "
                                >
                                    تأیید امنیت حساب
                                </div>

                                <h1
                                    class="email-title"
                                    style="
                                        margin: 0;
                                        color: #111827;
                                        font-size: 29px;
                                        line-height: 42px;
                                        font-weight: 900;
                                    "
                                >
                                    ایمیل خود را تأیید کنید
                                </h1>
                            </div>

                            <p
                                style="
                                    margin: 0 0 14px;
                                    color: #374151;
                                    font-size: 15px;
                                    line-height: 29px;
                                    font-weight: 700;
                                "
                            >
                                سلام
                                {{ filled($user->name) ? $user->name : 'دوست عزیز' }}،
                            </p>

                            <p
                                style="
                                    margin: 0;
                                    color: #6b7280;
                                    font-size: 14px;
                                    line-height: 28px;
                                "
                            >
                                از ثبت‌نام شما در ولورا خوشحالیم. برای تکمیل
                                ثبت‌نام و فعال‌شدن امکانات حساب، لطفاً آدرس
                                ایمیل خود را تأیید کنید.
                            </p>

                            {{-- ایمیل کاربر --}}
                            <table
                                role="presentation"
                                cellpadding="0"
                                cellspacing="0"
                                border="0"
                                width="100%"
                                style="
                                    margin-top: 24px;
                                    border: 1px solid #fbcfe8;
                                    border-radius: 16px;
                                    background-color: #fdf2f8;
                                "
                            >
                                <tr>
                                    <td
                                        style="
                                            padding: 15px 18px;
                                            color: #6b7280;
                                            font-size: 12px;
                                        "
                                    >
                                        ایمیل ثبت‌شده
                                    </td>
                                </tr>

                                <tr>
                                    <td
                                        dir="ltr"
                                        align="left"
                                        style="
                                            padding: 0 18px 15px;
                                            color: #be185d;
                                            font-family: Arial, sans-serif;
                                            font-size: 15px;
                                            font-weight: 700;
                                            word-break: break-all;
                                        "
                                    >
                                        {{ $user->email }}
                                    </td>
                                </tr>
                            </table>

                            {{-- دکمه تأیید --}}
                            <table
                                role="presentation"
                                cellpadding="0"
                                cellspacing="0"
                                border="0"
                                width="100%"
                                style="margin-top: 30px;"
                            >
                                <tr>
                                    <td align="center">
                                        <a
                                            href="{{ $verificationUrl }}"
                                            class="verify-button"
                                            style="
                                                display: inline-block;
                                                padding: 15px 30px;
                                                border-radius: 14px;
                                                background-color: #ec4899;
                                                background-image: linear-gradient(
                                                    135deg,
                                                    #ec4899,
                                                    #f43f5e
                                                );
                                                color: #ffffff;
                                                font-size: 15px;
                                                line-height: 22px;
                                                font-weight: 900;
                                                text-decoration: none;
                                                box-shadow: 0 8px 20px rgba(
                                                    236,
                                                    72,
                                                    153,
                                                    0.28
                                                );
                                            "
                                        >
                                            تأیید ایمیل و فعال‌سازی حساب
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <p
                                style="
                                    margin: 18px 0 0;
                                    text-align: center;
                                    color: #9ca3af;
                                    font-size: 12px;
                                    line-height: 22px;
                                "
                            >
                                این لینک تا
                                {{ $expiresInMinutes }}
                                دقیقه معتبر است.
                            </p>

                            {{-- راهنمای کوتاه --}}
                            <table
                                role="presentation"
                                cellpadding="0"
                                cellspacing="0"
                                border="0"
                                width="100%"
                                style="
                                    margin-top: 30px;
                                    border-radius: 16px;
                                    background-color: #f9fafb;
                                "
                            >
                                <tr>
                                    <td style="padding: 20px;">
                                        <div
                                            style="
                                                margin-bottom: 12px;
                                                color: #374151;
                                                font-size: 13px;
                                                font-weight: 900;
                                            "
                                        >
                                            بعد از تأیید چه اتفاقی می‌افتد؟
                                        </div>

                                        <div
                                            style="
                                                margin-bottom: 9px;
                                                color: #6b7280;
                                                font-size: 13px;
                                                line-height: 23px;
                                            "
                                        >
                                            <span
                                                style="
                                                    color: #ec4899;
                                                    font-weight: 900;
                                                "
                                            >
                                                ✓
                                            </span>

                                            دسترسی به داشبورد فعال می‌شود.
                                        </div>

                                        <div
                                            style="
                                                margin-bottom: 9px;
                                                color: #6b7280;
                                                font-size: 13px;
                                                line-height: 23px;
                                            "
                                        >
                                            <span
                                                style="
                                                    color: #ec4899;
                                                    font-weight: 900;
                                                "
                                            >
                                                ✓
                                            </span>

                                            امکان مشاهده پروفایل‌ها و ارسال
                                            پیام فعال می‌شود.
                                        </div>

                                        <div
                                            style="
                                                color: #6b7280;
                                                font-size: 13px;
                                                line-height: 23px;
                                            "
                                        >
                                            <span
                                                style="
                                                    color: #ec4899;
                                                    font-weight: 900;
                                                "
                                            >
                                                ✓
                                            </span>

                                            امنیت حساب شما افزایش پیدا می‌کند.
                                        </div>
                                    </td>
                                </tr>
                            </table>

                            {{-- هشدار امنیتی --}}
                            <table
                                role="presentation"
                                cellpadding="0"
                                cellspacing="0"
                                border="0"
                                width="100%"
                                style="
                                    margin-top: 22px;
                                    border: 1px solid #fde68a;
                                    border-radius: 14px;
                                    background-color: #fffbeb;
                                "
                            >
                                <tr>
                                    <td
                                        style="
                                            padding: 15px 17px;
                                            color: #92400e;
                                            font-size: 12px;
                                            line-height: 23px;
                                        "
                                    >
                                        <strong>
                                            نکته امنیتی:
                                        </strong>

                                        اگر شما در ولورا ثبت‌نام نکرده‌اید،
                                        نیازی به انجام کاری نیست و می‌توانید
                                        این ایمیل را نادیده بگیرید.
                                    </td>
                                </tr>
                            </table>

                            {{-- لینک جایگزین --}}
                            <div
                                style="
                                    margin-top: 26px;
                                    padding-top: 22px;
                                    border-top: 1px solid #f3f4f6;
                                "
                            >
                                <p
                                    style="
                                        margin: 0 0 10px;
                                        color: #9ca3af;
                                        font-size: 11px;
                                        line-height: 21px;
                                    "
                                >
                                    اگر دکمه بالا کار نکرد، آدرس زیر را
                                    کپی کرده و در مرورگر باز کنید:
                                </p>

                                <div
                                    dir="ltr"
                                    style="
                                        padding: 12px;
                                        border-radius: 10px;
                                        background-color: #f3f4f6;
                                        color: #6b7280;
                                        font-family: Arial, sans-serif;
                                        font-size: 10px;
                                        line-height: 18px;
                                        text-align: left;
                                        word-break: break-all;
                                    "
                                >
                                    {{ $verificationUrl }}
                                </div>
                            </div>
                        </td>
                    </tr>

                    {{-- فوتر --}}
                    <tr>
                        <td
                            align="center"
                            style="
                                padding: 24px;
                                border-top: 1px solid #fce7f3;
                                background-color: #fff7fa;
                            "
                        >
                            <p
                                style="
                                    margin: 0;
                                    color: #6b7280;
                                    font-size: 12px;
                                    line-height: 22px;
                                "
                            >
                                با احترام،
                                تیم
                                {{ config('app.name', 'Vlora') }}
                            </p>

                            <p
                                style="
                                    margin: 7px 0 0;
                                    color: #9ca3af;
                                    font-size: 10px;
                                    line-height: 18px;
                                "
                            >
                                © {{ now()->year }}
                                {{ config('app.name', 'Vlora') }}
                                — تمامی حقوق محفوظ است.
                            </p>

                            <p
                                style="
                                    margin: 5px 0 0;
                                    color: #d1d5db;
                                    font-size: 9px;
                                    line-height: 16px;
                                "
                            >
                                این ایمیل به‌صورت خودکار ارسال شده است.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>