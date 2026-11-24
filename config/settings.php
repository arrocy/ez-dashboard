<?php

$socialites = [];
$socialiteProviders = require __DIR__ . '/socialite.php';

foreach ($socialiteProviders['default'] as $provider) {
    $driver = $provider['driver'];
    $socialites["socialite_{$driver}_enabled"] = $provider['enabled'] ?? '';
    $socialites["socialite_{$driver}_client_id"] = $provider['client_id'] ?? '';
    $socialites["socialite_{$driver}_client_secret"] = $provider['client_secret'] ?? '';
    $socialites["socialite_{$driver}_redirect_uri"] = $provider['redirect_uri'] ?? '';
}

return [
    'default' => [
        'access_token' => '',
        'body_footer_enabled' => true,
        'company_name' => config('app.name'),
        'company_email' => config('app.admin_email'),
        'company_phone' => config('app.admin_phone'),
        'company_street' => '123 fake st.',
        'company_city' => 'Beverly Hills',
        'company_state' => 'CA',
        'company_zip' => '90210',
        'company_country' => 'USA',
        'company_avatar' => config('app.url') . '/img/favicon.png',
        'company_description' => 'My Company Short Description',
        'company_homepage' => 'default',
        'company_homepage_url' => '',
        'require_strong_password_enabled' => false,
        'user_create_account_enabled' => true,
        'user_delete_account_enabled' => false,
        'decimal_enabled' => true,
        'currency_code' => 'USD',
        'google_analytics_id' => 'G-X83CWG6SWW',
        'google_maps_embed_url' => '',
        'facebook_profile_url' => 'https://facebook.com/arrocy',
        'google_profile_url' => 'https://google.com/arrocy',
        'instagram_profile_url' => 'https://instagram.com/arrocy',
        'x_profile_url' => 'https://x.com/arrocy',
        'smtp_mailer_enabled' => false,
        'smtp_mailer_host' => 'smtp.gmail.com',
        'smtp_mailer_port' => '587',
        'smtp_mailer_encryption' => 'tls',
        'smtp_mailer_username' => 'visimisi.noreply@gmail.com',
        'smtp_mailer_password' => 'bvxhwkjqsfquxhek',
        'smtp_mailer_from_address' => 'visimisi.noreply@gmail.com',
        'smtp_mailer_from_name' => config('app.name'),
        // 's3_storage_enabled' => false,
        // 's3_storage_use_ssl_enabled' => true,
        // 's3_storage_port' => '',
        // 's3_storage_access_key' => '',
        // 's3_storage_secret_key' => '',
        // 's3_storage_bucket' => '',
        // 's3_storage_endpoint' => '',
        // 's3_storage_region' => '',
        ...$socialites,
    ],
];