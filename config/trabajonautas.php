<?php

return [
    'roles' => [
        'admin' => env('ADMIN_ROLE', 'ADMIN'),
        'user' => env('USER_ROLE', 'USER'),
        'client' => env('CLIENT_ROLE', 'CLIENT'),
    ],

    'support_phone' => env('SUPPORT_PHONE'),

    'seed_users' => [
        'admin' => [
            'name' => env('SEED_ADMIN_NAME', 'Administrador'),
            'email' => env('SEED_ADMIN_EMAIL'),
            'password' => env('SEED_ADMIN_PASSWORD'),
            'phone' => env('SEED_ADMIN_PHONE'),
        ],
        'user' => [
            'name' => env('SEED_USER_NAME', 'Usuario'),
            'email' => env('SEED_USER_EMAIL'),
            'password' => env('SEED_USER_PASSWORD'),
            'phone' => env('SEED_USER_PHONE'),
        ],
    ],
];
