<?php

namespace VirtualClick\AdAuthClient;

use Illuminate\Support\ServiceProvider;
use VirtualClick\AdAuthClient\Contracts\AuthenticationInterface;
use VirtualClick\AdAuthClient\Services\AuthenticationResponseService;
use VirtualClick\AdAuthClient\Services\AuthenticationService;

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

        $this->app->singleton(AuthenticationResponseService::class);

        $this->app->singleton(AuthenticationInterface::class, function ($app) {
            return new AuthenticationService(
                $app->make(AuthenticationResponseService::class)
            );
        });

        $this->app->bind('ad-auth', function ($app) {
            return $app->make(AuthenticationInterface::class);
        });
    }
}
