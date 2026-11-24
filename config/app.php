<?php

return [
    'admin_id' => env('ADMIN_ID', 1625120100),
    'admin_email' => env('ADMIN_EMAIL', 'admin@test.com'),
    'admin_name' => env('ADMIN_NAME', 'Administrator'),
    'admin_phone' => env('ADMIN_PHONE', '1234567890'),
    'admin_password' => env('ADMIN_PASSWORD', 'password'),
    'version' => '0.1.0',

    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    |
    | This value is the name of your application, which will be used when the
    | framework needs to place the application's name in a notification or
    | other UI elements where an application name needs to be displayed.
    |
    */

    'name' => env('APP_NAME', 'Laravel'),

    /*
    |--------------------------------------------------------------------------
    | Application Environment
    |--------------------------------------------------------------------------
    |
    | This value determines the "environment" your application is currently
    | running in. This may determine how you prefer to configure various
    | services the application utilizes. Set this in your ".env" file.
    |
    */

    'env' => env('APP_ENV', 'production'),

    /*
    |--------------------------------------------------------------------------
    | Application Debug Mode
    |--------------------------------------------------------------------------
    |
    | When your application is in debug mode, detailed error messages with
    | stack traces will be shown on every error that occurs within your
    | application. If disabled, a simple generic error page is shown.
    |
    */

    'debug' => (bool) env('APP_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | Application URL
    |--------------------------------------------------------------------------
    |
    | This URL is used by the console to properly generate URLs when using
    | the Artisan command line tool. You should set this to the root of
    | the application so that it's available within Artisan commands.
    |
    */

    'url' => env('APP_URL', 'http://localhost'),

    /*
    |--------------------------------------------------------------------------
    | Application Timezone
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default timezone for your application, which
    | will be used by the PHP date and date-time functions. The timezone
    | is set to "UTC" by default as it is suitable for most use cases.
    |
    */

    'timezone' => 'UTC',

    /*
    |--------------------------------------------------------------------------
    | Application Locale Configuration
    |--------------------------------------------------------------------------
    |
    | The application locale determines the default locale that will be used
    | by Laravel's translation / localization methods. This option can be
    | set to any locale for which you plan to have translation strings.
    |
    */

    'locale' => env('APP_LOCALE', 'en'),

    'fallback_locale' => env('APP_FALLBACK_LOCALE', 'en'),

    'faker_locale' => env('APP_FAKER_LOCALE', 'en_US'),

    /*
    |--------------------------------------------------------------------------
    | Encryption Key
    |--------------------------------------------------------------------------
    |
    | This key is utilized by Laravel's encryption services and should be set
    | to a random, 32 character string to ensure that all encrypted values
    | are secure. You should do this prior to deploying the application.
    |
    */

    'cipher' => 'AES-256-CBC',

    'key' => env('APP_KEY'),

    'previous_keys' => [
        ...array_filter(
            explode(',', (string) env('APP_PREVIOUS_KEYS', ''))
        ),
    ],

    /*
    |--------------------------------------------------------------------------
    | Maintenance Mode Driver
    |--------------------------------------------------------------------------
    |
    | These configuration options determine the driver used to determine and
    | manage Laravel's "maintenance mode" status. The "cache" driver will
    | allow maintenance mode to be controlled across multiple machines.
    |
    | Supported drivers: "file", "cache", "array"
    |
    */

    'maintenance' => [
        'driver' => env('APP_MAINTENANCE_DRIVER', 'file'),
        'store' => env('APP_MAINTENANCE_STORE', 'database'),
    ],

    'timezones' => [
        'Pacific/Pago_Pago'   => '(UTC-11:00) Midway Island, Samoa',
        'Pacific/Honolulu'    => '(UTC-10:00) Hawaii',
        'America/Anchorage'   => '(UTC-09:00) Alaska',
        'America/Los_Angeles' => '(UTC-08:00) Pacific Time (US & Canada)',
        'America/Denver'      => '(UTC-07:00) Mountain Time (US & Canada)',
        'America/Chicago'     => '(UTC-06:00) Central Time (US & Canada)',
        'America/New_York'    => '(UTC-05:00) Eastern Time (US & Canada)',
        'America/Bogota'      => '(UTC-05:00) Bogota',
        'America/Caracas'     => '(UTC-04:00) Caracas, La Paz, Santiago',
        'America/Sao_Paulo'   => '(UTC-03:00) Brasilia',
        'America/Argentina/Buenos_Aires' => '(UTC-03:00) Buenos Aires',
        'America/Noronha'     => '(UTC-02:00) Mid-Atlantic',
        'Atlantic/Azores'     => '(UTC-01:00) Azores',
        'Atlantic/Cape_Verde' => '(UTC-01:00) Cape Verde Is.',
        'UTC'                 => '(UTC+00:00) UTC',
        'Europe/London'       => '(UTC+00:00) Edinburgh, London',
        'Africa/Casablanca'   => '(UTC+01:00) Casablanca',
        'Africa/Lagos'        => '(UTC+01:00) Lagos',
        'Europe/Amsterdam'    => '(UTC+01:00) Amsterdam, Berlin, Rome',
        'Europe/Madrid'       => '(UTC+01:00) Madrid, Paris',
        'Africa/Cairo'        => '(UTC+02:00) Cairo',
        'Africa/Johannesburg' => '(UTC+02:00) Johannesburg',
        'Europe/Athens'       => '(UTC+02:00) Athens, Bucharest',
        'Africa/Nairobi'      => '(UTC+03:00) Nairobi',
        'Asia/Riyadh'         => '(UTC+03:00) Kuwait, Riyadh',
        'Europe/Moscow'       => '(UTC+03:00) Moscow, St. Petersburg',
        'Asia/Dubai'          => '(UTC+04:00) Abu Dhabi, Muscat',
        'Asia/Kolkata'        => '(UTC+05:30) Chennai, Kolkata, Mumbai, New Delhi',
        'Asia/Dhaka'          => '(UTC+06:00) Astana, Dhaka',
        'Asia/Bangkok'        => '(UTC+07:00) Bangkok, Hanoi, Jakarta',
        'Asia/Jakarta'        => '(UTC+07:00) Western Indonesia Time (WIB)',
        'Asia/Hong_Kong'      => '(UTC+08:00) Beijing, Hong Kong',
        'Asia/Singapore'      => '(UTC+08:00) Kuala Lumpur, Singapore',
        'Australia/Perth'     => '(UTC+08:00) Perth',
        'Asia/Tokyo'          => '(UTC+09:00) Osaka, Sapporo, Tokyo',
        'Australia/Adelaide'  => '(UTC+09:30) Adelaide',
        'Australia/Darwin'    => '(UTC+09:30) Darwin',
        'Australia/Brisbane'  => '(UTC+10:00) Brisbane',
        'Australia/Melbourne' => '(UTC+10:00) Melbourne, Sydney',
        'Pacific/Guadalcanal' => '(UTC+11:00) Solomon Is., New Caledonia',
        'Pacific/Auckland'    => '(UTC+12:00) Auckland, Wellington',
        'Pacific/Fiji'        => '(UTC+12:00) Fiji',
    ],
];
