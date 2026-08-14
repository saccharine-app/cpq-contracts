<?php

namespace Saccharine\CPQ;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;

class CpqServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Load the migrations we built earlier
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        // Map the string 'context' to whatever generic model we use for the demo
        Relation::enforceMorphMap([
            'context' => \Saccharine\CPQ\Models\DemoContext::class,
        ]);
        
        // Allow host apps to publish the config file if they want to override defaults
        if ($this->app->runningInConsole()) {
            // Register custom artisan commands
            $this->commands([
                \Saccharine\CPQ\Console\Commands\SeedDemoCatalogCommand::class,
            ]);

            // Publish the config file to the host app
            $this->publishes([
                __DIR__.'/../config/cpq.php' => config_path('cpq.php'),
            ], 'cpq-config');

            // Publish the Vue pages to the host app
            $this->publishes([
                __DIR__.'/../resources/js/Pages' => resource_path('js/Pages'),
            ], 'cpq-views');
        }
    }

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/cpq.php', 'cpq');
    }
}