<?php

namespace Saccharine\CPQ\Filament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Filament\Navigation\NavigationItem;
use Saccharine\CPQ\Models\DemoContext;

class CpqFilamentPlugin implements Plugin
{
    public function getId(): string
    {
        return 'saccharine-cpq';
    }

    public function register(Panel $panel): void
    {
        // Conditionally add a navigation item to the host app's Filament panel
        /* $panel->navigationItems([
            NavigationItem::make('Quote Builder (Vue)')
                ->icon('heroicon-o-calculator')
                ->group('CPQ Engine')
                ->url(fn () => $this->getDemoUrl())
                ->openUrlInNewTab(false),
        ]); */
    }

    public function boot(Panel $panel): void
    {
        //
    }

    public static function make(): static
    {
        return app(static::class);
    }

    protected function getDemoUrl(): string
    {
        // Grab the first dummy context seeded by our command
        $context = DemoContext::first();
        
        if (! $context) {
            return '/';
        }

        return url("/cpq/selector/context/{$context->id}");
    }
}