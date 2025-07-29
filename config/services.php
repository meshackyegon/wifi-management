<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    // SMS Services
    'africastalking' => [
        'username' => env('AFRICASTALKING_USERNAME'),
        'api_key' => env('AFRICASTALKING_API_KEY'),
        'sender_id' => env('AFRICASTALKING_SENDER_ID', 'HOTSPOT'),
    ],

    'twilio' => [
        'sid' => env('TWILIO_SID'),
        'token' => env('TWILIO_TOKEN'),
        'from' => env('TWILIO_FROM'),
    ],

    // Mobile Money Services
    'mtn' => [
        'api_key' => env('MTN_API_KEY'),
        'api_secret' => env('MTN_API_SECRET'),
        'endpoint' => env('MTN_ENDPOINT', 'https://sandbox.momodeveloper.mtn.com'),
        'subscription_key' => env('MTN_SUBSCRIPTION_KEY'),
    ],

    'airtel' => [
        'client_id' => env('AIRTEL_CLIENT_ID'),
        'client_secret' => env('AIRTEL_CLIENT_SECRET'),
        'api_key' => env('AIRTEL_API_KEY'),
        'endpoint' => env('AIRTEL_ENDPOINT', 'https://openapi.airtel.africa'),
    ],

    'safaricom' => [
        'consumer_key' => env('SAFARICOM_CONSUMER_KEY'),
        'consumer_secret' => env('SAFARICOM_CONSUMER_SECRET'),
        'shortcode' => env('SAFARICOM_SHORTCODE'),
        'passkey' => env('SAFARICOM_PASSKEY'),
        'endpoint' => env('SAFARICOM_ENDPOINT', 'https://sandbox.safaricom.co.ke'),
        'access_token' => env('SAFARICOM_ACCESS_TOKEN'),
        'callback_url' => env('MPESA_STK_CALLBACK_URL'),
    ],

    'vodacom' => [
        'api_key' => env('VODACOM_API_KEY'),
        'api_secret' => env('VODACOM_API_SECRET'),
        'shortcode' => env('VODACOM_SHORTCODE'),
        'endpoint' => env('VODACOM_ENDPOINT'),
    ],

    'tigo' => [
        'merchant_id' => env('TIGO_MERCHANT_ID'),
        'api_key' => env('TIGO_API_KEY'),
        'api_secret' => env('TIGO_API_SECRET'),
        'endpoint' => env('TIGO_ENDPOINT'),
    ],

    'orange' => [
        'api_key' => env('ORANGE_API_KEY'),
        'api_secret' => env('ORANGE_API_SECRET'),
        'merchant_id' => env('ORANGE_MERCHANT_ID'),
        'endpoint' => env('ORANGE_ENDPOINT'),
    ],

];
