<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Login Methods Configuration
    |--------------------------------------------------------------------------
    |
    | Configure which login methods are enabled for this application.
    | Google OAuth is always enabled as the primary login method.
    |
    */

    'login_methods' => [
        'google' => true,  // Always enabled as primary
        'password' => env('AUTH_PASSWORD_ENABLED', true),
        'whatsapp' => env('AUTH_WHATSAPP_ENABLED', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | WhatsApp OTP Configuration
    |--------------------------------------------------------------------------
    |
    | Configure the WhatsApp gateway for OTP login.
    | Supported: "fonnte", "wablas"
    |
    */

    'whatsapp_gateway' => env('WHATSAPP_GATEWAY', 'fonnte'),

    'whatsapp_api_key' => env('WHATSAPP_API_KEY'),

];
