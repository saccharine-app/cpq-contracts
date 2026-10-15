<?php

namespace Workbench\App\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * Workbench host application service provider for the CPQ package.
 *
 * The host app normally provides the Inertia root view (`app.blade.php`) that
 * wraps every Inertia page. Inside Workbench we register the workbench
 * resources/views directory as a view location so `Inertia::render()` in the
 * selector route resolves its root view without a host application.
 */
class WorkbenchServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Register the workbench views as a plain view location so the default
        // Inertia root view name (`app`) resolves to workbench/resources/views.
        app('view')->addLocation(__DIR__ . '/../../resources/views');
    }
}
