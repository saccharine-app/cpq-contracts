<?php

use Illuminate\Support\Facades\Route;
use Saccharine\CPQ\Http\Controllers\QuoteBuilderController;
use Saccharine\CPQ\Http\Controllers\SaveQuoteController;

Route::middleware(['web'])
    ->group(function () {
        // The JSON Rules Manifest endpoint (can be consumed by Vue, React, or external CMS)
        Route::get('/manifest/{ownerType}/{ownerId}', [QuoteBuilderController::class, 'manifest'])
            ->name('cpq.manifest');

        // The Inertia Selector UI (if the host app wants to render the built-in view)
        Route::get('/selector/{ownerType}/{ownerId}/{quoteId?}', 
                [QuoteBuilderController::class, 'create'])
            ->name('cpq.selector');

        // The Save endpoint
        Route::post('/quotes', [SaveQuoteController::class, 'store'])
            ->name('cpq.quotes.store');
    });