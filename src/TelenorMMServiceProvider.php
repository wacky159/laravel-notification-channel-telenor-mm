<?php

declare(strict_types=1);

namespace NotificationChannels\TelenorMM;

use Illuminate\Support\ServiceProvider;
use Illuminate\Notifications\ChannelManager;
use Illuminate\Support\Facades\Notification;
use NotificationChannels\TelenorMM\Contracts\AuthorizationCodeProvider;
use NotificationChannels\TelenorMM\Support\DefaultAuthorizationCodeProvider;

/**
 * TelenorMM notification channel service provider
 */
class TelenorMMServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Publish the configuration file
        $this->publishes([
            __DIR__.'/../config/telenor-mm.php' => config_path('telenor-mm.php'),
        ], 'config');

        // Register the notification driver
        Notification::resolved(function (ChannelManager $service) {
            $service->extend('telenor-mm', fn ($app) => $app->make(TelenorMMChannel::class));
        });
    }

    public function register(): void
    {
        // Merge the configuration file
        $this->mergeConfigFrom(
            __DIR__.'/../config/telenor-mm.php', 'telenor-mm'
        );

        $this->app->singleton(AuthorizationCodeProvider::class, fn ($app) => new DefaultAuthorizationCodeProvider());

        $this->app->singleton(TelenorMMChannel::class, fn ($app) => new TelenorMMChannel($app->make(AuthorizationCodeProvider::class)));
    }
}
