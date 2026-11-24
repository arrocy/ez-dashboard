<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use SocialiteProviders\Instagram\Provider as InstagramProvider;
use SocialiteProviders\Manager\SocialiteWasCalled;

class SocialiteServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Event::listen(function (SocialiteWasCalled $event) {
            $event->extendSocialite('instagram', InstagramProvider::class);
        });

        if (! Schema::hasTable('settings')) {
            return;
        }

        $config = [];

        foreach (config('socialite.default') as $provider) {
            $driver = $provider['driver'];
            $config["services.{$driver}.client_id"] = setting("socialite_{$driver}_client_id", $provider['client_id'] ?? '');
            $config["services.{$driver}.client_secret"] = setting("socialite_{$driver}_client_secret", $provider['client_secret'] ?? '');
            $config["services.{$driver}.redirect"] = setting("socialite_{$driver}_redirect_uri", $provider['redirect_uri'] ?? '');
        }

        config($config);
    }
}
