<?php

namespace Saccharine\CPQ\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Orchestra\Testbench\Concerns\WithWorkbench;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TestCase extends Orchestra
{
    use WithWorkbench;
    use RefreshDatabase;

    /**
     * Enable package auto-discovery so inertia-laravel (and its Inertia root
     * view directive / testing helpers) boots exactly as it would in a real
     * host application, without hard-coding its provider in testbench.yaml.
     */
    protected $enablesPackageDiscoveries = true;

    protected function setUp(): void
    {
        parent::setUp();

        // Loads the testbench skeleton's core migrations (users, cache, jobs).
        $this->loadLaravelMigrations();
    }

    /**
     * Define environment setup.
     *
     * Workbench reads package providers from `testbench.yaml`; this hook only
     * tunes runtime config for the test environment.
     */
    protected function getEnvironmentSetUp($app)
    {
        // Provide a standard 32-character base64 string for Laravel's encrypter
        $app['config']->set('app.key', 'base64:Hupx3yAySikrM2/edkZQNQHslgDWYvnkFmTNFjq+95U=');

        // Point Inertia's testing page finder at the workbench Vue pages so
        // `assertInertia()->component('CPQ/Selector')` can resolve the page
        // file (a host app would compile these from resources/js/Pages).
        $app['config']->set('inertia.testing.page_paths', [
            __DIR__ . '/../workbench/resources/js/Pages',
        ]);

        // Force all database connections to use in-memory sqlite
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.sqlite.database', ':memory:');
        $app['config']->set('database.connections.testing', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
    }
}
