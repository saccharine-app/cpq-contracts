<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Currency
    |--------------------------------------------------------------------------
    |
    | The default currency used for catalog items and contract line items.
    | 
    */
    'currency' => 'CAD',

    
    /*
    |--------------------------------------------------------------------------
    | Routes
    |--------------------------------------------------------------------------
    |
    | The default route used for cpq URLs.
    | 
    */
    'routes' => [
        'prefix' => 'cpq',
        'middleware' => ['web'], // Host app can change this to ['web', 'auth']
    ],
];