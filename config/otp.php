<?php

return [

    /*
    |--------------------------------------------------------------------------
    | One-Time Password Policy
    |--------------------------------------------------------------------------
    |
    | Mobile OTPs are numeric codes delivered by SMS. Only a hash of each code
    | is stored, codes expire quickly and each code allows a small number of
    | verification attempts before the candidate has to request a new one.
    |
    */

    'code_length' => (int) env('OTP_CODE_LENGTH', 6),

    'ttl_minutes' => (int) env('OTP_TTL_MINUTES', 10),

    'resend_seconds' => (int) env('OTP_RESEND_SECONDS', 60),

    'max_attempts' => (int) env('OTP_MAX_ATTEMPTS', 5),

    'request_per_hour' => (int) env('OTP_REQUEST_PER_HOUR', 5),

    'verify_per_ten_minutes' => (int) env('OTP_VERIFY_PER_TEN_MINUTES', 10),

    /*
    |--------------------------------------------------------------------------
    | SMS Delivery
    |--------------------------------------------------------------------------
    |
    | `twilio` sends real SMS through the Twilio Programmable Messaging API.
    | `log` writes the message to the application log and is only meant for
    | local development and tests.
    |
    */

    'sms_driver' => env('SMS_DRIVER', 'twilio'),

    'default_country_code' => env('SMS_DEFAULT_COUNTRY_CODE', '+91'),

    'message' => env('OTP_SMS_MESSAGE', 'Your GYRS verification code is :code. It expires in :minutes minutes. Do not share this code.'),

];
