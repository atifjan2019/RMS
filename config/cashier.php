<?php

return [
    'key' => env('STRIPE_KEY'),
    'secret' => env('STRIPE_SECRET'),
    'webhook' => [
        'secret' => env('STRIPE_WEBHOOK_SECRET'),
        'tolerance' => env('STRIPE_WEBHOOK_TOLERANCE', 300),
    ],

    // Tell Cashier which model is billable for subscriptions.
    // This project bills Tenants, not individual Users.
    'model' => App\Models\Tenant::class,

    'currency' => env('CASHIER_CURRENCY', 'usd'),
    'currency_locale' => env('CASHIER_CURRENCY_LOCALE', 'en'),
    'logger' => env('CASHIER_LOGGER'),
];
