<?php

return [
    'default' => [
        [
            'driver' => 'facebook',
            'enabled' => false,
            'client_id' => env('FACEBOOK_CLIENT_ID', env('FACEBOOK_CLIENT_ID', '')),
            'client_secret' => env('FACEBOOK_CLIENT_SECRET', env('FACEBOOK_CLIENT_SECRET', '')),
            'redirect_uri' => env('FACEBOOK_REDIRECT_URI', env('APP_URL') . '/login/facebook/callback'),
        ],
        [
            'driver' => 'google',
            'enabled' => false,
            'client_id' => env('GOOGLE_CLIENT_ID', env('GOOGLE_CLIENT_ID', '')),
            'client_secret' => env('GOOGLE_CLIENT_SECRET', env('GOOGLE_CLIENT_SECRET', '')),
            'redirect_uri' => env('GOOGLE_REDIRECT_URI', env('APP_URL') . '/login/google/callback'),
        ],
        [
            'driver' => 'instagram',
            'enabled' => false,
            'client_id' => env('INSTAGRAM_CLIENT_ID', env('INSTAGRAM_CLIENT_ID', '')),
            'client_secret' => env('INSTAGRAM_CLIENT_SECRET', env('INSTAGRAM_CLIENT_SECRET', '')),
            'redirect_uri' => env('INSTAGRAM_REDIRECT_URI', env('APP_URL') . '/login/instagram/callback'),
        ],
        [
            'driver' => 'x',
            'enabled' => false,
            'client_id' => env('X_CLIENT_ID', env('X_CLIENT_ID', '')),
            'client_secret' => env('X_CLIENT_SECRET', env('X_CLIENT_SECRET', '')),
            'redirect_uri' => env('X_REDIRECT_URI', env('APP_URL') . '/login/x/callback'),
        ],
    ],
];