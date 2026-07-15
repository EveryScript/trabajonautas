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

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],
    // Servidor de Google para Inicio de Sesión
    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URI')
    ],
    // ID de Google Analytics
    'google_analytics' => [
        'id' => env('GOOGLE_ANALYTICS_ID')
    ],
    // VAPID Public Notification Firebace Key
    'firebase' => [
        'vapid_key' => env('FIREBASE_VAPID_KEY'),
    ],
    // Baneco Service
    'baneco' => [
        'url'      => env('BANECO_URL'),
        'username' => env('BANECO_USERNAME'),
        'password' => env('BANECO_PASSWORD'),
        'aes_key'  => env('BANECO_AES_KEY'),
        'account'  => env('BANECO_ACCOUNT'),
    ],
];
