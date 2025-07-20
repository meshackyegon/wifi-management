<?php

return [
    'default_provider' => env('SMS_DEFAULT_PROVIDER', 'africastalking'),
    
    'providers' => [
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
    ],
    
    'rate_limiting' => [
        'max_per_minute' => 60,
        'max_per_hour' => 1000,
    ],
];
