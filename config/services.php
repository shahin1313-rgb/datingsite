<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),

        'secret' => env('AWS_SECRET_ACCESS_KEY'),

        'region' => env(
            'AWS_DEFAULT_REGION',
            'us-east-1'
        ),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Cloudflare Turnstile
    |--------------------------------------------------------------------------
    */

    'turnstile' => [
        'site_key' => env('TURNSTILE_SITE_KEY'),

        'secret_key' => env('TURNSTILE_SECRET_KEY'),

        'verify_url' => env(
            'TURNSTILE_VERIFY_URL',
            'https://challenges.cloudflare.com/turnstile/v0/siteverify'
        ),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' =>
                env('SLACK_BOT_USER_OAUTH_TOKEN'),

            'channel' =>
                env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'premium_payment' => [
        'rpc_url' => env(
            'PREMIUM_BSC_RPC_URL',
            'https://bsc-dataseed.bnbchain.org'
        ),

        'wallet_address' =>
            env('PREMIUM_BSC_WALLET_ADDRESS'),

        'minimum_amount_atomic' => env(
            'PREMIUM_USDT_MIN_AMOUNT_ATOMIC',
            '500000000000000000'
        ),

        'confirmations' => (int) env(
            'PREMIUM_BSC_CONFIRMATIONS',
            12
        ),

        'premium_days' => (int) env(
            'PREMIUM_DAYS',
            30
        ),
    ],

];