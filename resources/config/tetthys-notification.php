<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Default channels (used when trigger() gets no override)
    |--------------------------------------------------------------------------
    */
    'default_channels' => ['email'],

    /*
    |--------------------------------------------------------------------------
    | Channel bindings map
    |--------------------------------------------------------------------------
    | key: channel name
    | value: container id (class-string or binding key)
    */
    'channels' => [
        'email' => \App\Notifications\Channels\EmailChannel::class,
        // 'sms' => \App\Notifications\Channels\SmsChannel::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Queue settings
    |--------------------------------------------------------------------------
    */
    'queue' => [
        'connection' => null, // null = default
        'queue' => null,      // null = default
    ],

    /*
    |--------------------------------------------------------------------------
    | Delivery idempotency store
    |--------------------------------------------------------------------------
    */
    'store' => [
        'cache' => [
            'prefix' => 'tetthys:notification:delivery:',
            'ttl_seconds' => 60 * 60 * 24 * 7, // 7 days
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Defaults for simple Preferences implementation
    |--------------------------------------------------------------------------
    */
    'preferences' => [
        'disabled_channels' => [], // global default
        'locale' => null,          // null => app()->getLocale()
    ],
];
