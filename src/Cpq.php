<?php

namespace Package\CPQ;

use Illuminate\Support\Facades\Route;
use Package\CPQ\Http\Controllers\QuoteBuilderController;
use Package\CPQ\Http\Controllers\SaveQuoteController;

class Cpq
{
    /**
     * Binds the CPQ routes into the host application.
     */
    public static function routes(array $options = []): void
    {
        Route::group($options, function () {
            // The JSON Rules Manifest endpoint (can be consumed by Vue, React, or external CMS)
            Route::get('/manifest/{ownerType}/{ownerId}', [QuoteBuilderController::class, 'manifest'])
                ->name('cpq.manifest');

            // The Inertia Selector UI (if the host app wants to render the built-in view)
            Route::get('/selector/{ownerType}/{ownerId}', [QuoteBuilderController::class, 'create'])
                ->name('cpq.selector');

            // The Save endpoint
            Route::post('/quotes', [SaveQuoteController::class, 'store'])
                ->name('cpq.quotes.store');
        });
    }
}