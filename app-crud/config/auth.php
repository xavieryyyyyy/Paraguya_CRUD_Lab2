<?php

// Session web guard, Eloquent User provider, and password broker settings. Reset expiry is in
// minutes; throttle is in seconds. Role/admin checks are not defined here.

use App\Models\User;

return [

    // Select the normal guard and password broker unless the environment overrides them.
    'defaults' => [
        'guard' => env('AUTH_GUARD', 'web'),
        'passwords' => env('AUTH_PASSWORD_BROKER', 'users'),
    ],

    // The web guard authenticates browser requests with sessions and the users provider.
    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],
    ],

    // Eloquent loads accounts through the configured User model and its users table.
    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => env('AUTH_MODEL', User::class),
        ],

        // 'users' => [ 'driver' => 'database', 'table' => 'users', ],
    ],

    // The users broker stores reset-token hashes in the configured table.
    // expire is minutes; throttle is seconds between reset-link requests.
    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => env('AUTH_PASSWORD_RESET_TOKEN_TABLE', 'password_reset_tokens'),
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    'password_timeout' => env('AUTH_PASSWORD_TIMEOUT', 10800),

];
