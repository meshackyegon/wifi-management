<?php

return [
    'default_provider' => env('SMS_DEFAULT_PROVIDER', 'jambopay'),
    
    // Admin notification phone number for purchase alerts
    'admin_notification_phone' => env('SMS_ADMIN_PHONE', '0722617737'),
    
    'providers' => [
        'jambopay' => [
            'client_id' => env('JAMBOPAY_CLIENT_ID'),
            'client_secret' => env('JAMBOPAY_CLIENT_SECRET'),
            'sender_name' => env('JAMBOPAY_SENDER_NAME', 'VESEN'),
            'auth_url' => 'https://accounts.jambopay.com/auth/token',
            'send_url' => 'https://swift.jambopay.co.ke/api/public/send',
            'callback_url' => env('JAMBOPAY_CALLBACK_URL', 'https://angacinemas.com/send_sms/callback.php'),
        ],
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
