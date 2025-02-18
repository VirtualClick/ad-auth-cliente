<?php

namespace VirtualClick\AdAuthClient;

use Illuminate\Support\ServiceProvider;
use VirtualClick\AdAuthClient\Contracts\AuthenticationInterface;

class AdAuthServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->publishes([
            __DIR__ . '/../config/ad-auth.php' => config_path('ad-auth.php'),
        ], 'config');

        $this->mergeConfigFrom(
            __DIR__ . '/../config/ad-auth.php',
            'ad-auth'
        );

        $this->app->bind(
            AuthenticationInterface::class,
            Authentication::class
        );

        $this->app->bind('ad-auth', function ($app) {
            return $app->make(AuthenticationInterface::class);
        });
    }
}
