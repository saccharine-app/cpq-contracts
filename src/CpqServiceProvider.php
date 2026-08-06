<?php

namespace Package\CPQ;

use Illuminate\Support\ServiceProvider;

class CpqServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Load the migrations we built earlier
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        // Allow host apps to publish the config file if they want to override defaults
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/cpq.php' => config_path('cpq.php'),
            ], 'cpq-config');
        }
    }

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/cpq.php', 'cpq');
    }
}