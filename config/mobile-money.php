<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Mobile Money Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for mobile money providers including sandbox and production
    | settings for M-Pesa, MTN Mobile Money, Airtel Money, and others.
    |
    */

    'default' => env('MOBILE_MONEY_DEFAULT', 'safaricom'),

    'providers' => [
        'safaricom_mpesa' => [
            'name' => 'Safaricom M-Pesa',
            'enabled' => true,
            'config' => [
                'consumer_key' => env('SAFARICOM_CONSUMER_KEY'),
                'consumer_secret' => env('SAFARICOM_CONSUMER_SECRET'),
                'shortcode' => env('SAFARICOM_SHORTCODE', '174379'),
                'passkey' => env('SAFARICOM_PASSKEY'),
                'endpoint' => env('SAFARICOM_ENDPOINT', 'https://sandbox.safaricom.co.ke'),
                'callback_url' => env('APP_URL') . '/mobile-money/callback/safaricom',
            ],
            'test_numbers' => [
                'success' => '+254708374149',
                'insufficient_funds' => '+254708374150',
                'invalid_account' => '+254708374151',
            ],
        ],

        'mtn_mobile_money' => [
            'name' => 'MTN Mobile Money',
            'enabled' => true,
            'config' => [
                'api_key' => env('MTN_API_KEY'),
                'api_secret' => env('MTN_API_SECRET'),
                'subscription_key' => env('MTN_SUBSCRIPTION_KEY'),
                'endpoint' => env('MTN_ENDPOINT', 'https://sandbox.momodeveloper.mtn.com'),
                'callback_url' => env('APP_URL') . '/mobile-money/callback/mtn',
            ],
            'test_numbers' => [
                'success' => '+256772123456',
                'failed' => '+256772123457',
            ],
        ],

        'airtel_money' => [
            'name' => 'Airtel Money',
            'enabled' => true,
            'config' => [
                'client_id' => env('AIRTEL_CLIENT_ID'),
                'client_secret' => env('AIRTEL_CLIENT_SECRET'),
                'api_key' => env('AIRTEL_API_KEY'),
                'endpoint' => env('AIRTEL_ENDPOINT', 'https://openapiuat.airtel.africa'),
                'callback_url' => env('APP_URL') . '/mobile-money/callback/airtel',
            ],
            'test_numbers' => [
                'success' => '+256701123456',
                'failed' => '+256701123457',
            ],
        ],

        'vodacom_mpesa' => [
            'name' => 'Vodacom M-Pesa',
            'enabled' => true,
            'config' => [
                'api_key' => env('VODACOM_API_KEY'),
                'api_secret' => env('VODACOM_API_SECRET'),
                'shortcode' => env('VODACOM_SHORTCODE'),
                'endpoint' => env('VODACOM_ENDPOINT', 'https://openapi.vodacom.co.tz/sandbox'),
                'callback_url' => env('APP_URL') . '/mobile-money/callback/vodacom',
            ],
            'test_numbers' => [
                'success' => '+255754123456',
                'failed' => '+255754123457',
            ],
        ],

        'tigo_pesa' => [
            'name' => 'Tigo Pesa',
            'enabled' => true,
            'config' => [
                'merchant_id' => env('TIGO_MERCHANT_ID'),
                'api_key' => env('TIGO_API_KEY'),
                'api_secret' => env('TIGO_API_SECRET'),
                'endpoint' => env('TIGO_ENDPOINT', 'https://tigopesa-api.tigo.co.tz/sandbox'),
                'callback_url' => env('APP_URL') . '/mobile-money/callback/tigo',
            ],
            'test_numbers' => [
                'success' => '+255652123456',
                'failed' => '+255652123457',
            ],
        ],

        'orange_money' => [
            'name' => 'Orange Money',
            'enabled' => true,
            'config' => [
                'api_key' => env('ORANGE_API_KEY'),
                'api_secret' => env('ORANGE_API_SECRET'),
                'merchant_id' => env('ORANGE_MERCHANT_ID'),
                'endpoint' => env('ORANGE_ENDPOINT', 'https://api.orange.com/sandbox'),
                'callback_url' => env('APP_URL') . '/mobile-money/callback/orange',
            ],
            'test_numbers' => [
                'success' => '+225012345678',
                'failed' => '+225012345679',
            ],
        ],
    ],

    'sandbox_mode' => env('MOBILE_MONEY_SANDBOX', true),

    'timeout' => env('MOBILE_MONEY_TIMEOUT', 30), // seconds

    'retry_attempts' => env('MOBILE_MONEY_RETRY_ATTEMPTS', 3),

    'service_fee_percentage' => env('MOBILE_MONEY_SERVICE_FEE', 3.0), // 3%
];
