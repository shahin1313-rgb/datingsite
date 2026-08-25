<?php

return [

    'defaults' => [
        'guard' => env(
            'AUTH_GUARD',
            'web'
        ),

        'passwords' => env(
            'AUTH_PASSWORD_BROKER',
            'users'
        ),
    ],

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],
    ],

    'providers' => [
        'users' => [
            'driver' => 'eloquent',

            'model' => env(
                'AUTH_MODEL',
                App\Models\User::class
            ),
        ],
    ],

    'passwords' => [
        'users' => [
            'provider' => 'users',

            'table' => env(
                'AUTH_PASSWORD_RESET_TOKEN_TABLE',
                'password_reset_tokens'
            ),

            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    'password_timeout' => env(
        'AUTH_PASSWORD_TIMEOUT',
        10800
    ),

    /*
     * مدت اعتبار تأیید دومرحله‌ای مدیر، به ثانیه.
     * مقدار پیش‌فرض ۸ ساعت است.
     */
    'admin_two_factor_timeout' => env(
        'ADMIN_TWO_FACTOR_TIMEOUT',
        28800
    ),

];