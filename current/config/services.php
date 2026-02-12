<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Image Services
    |--------------------------------------------------------------------------
    |
    | API keys for image services used for automatic product images
    |
    */

    'pixabay' => [
        'key' => env('PIXABAY_API_KEY'),
        // Get your free API key at: https://pixabay.com/api/docs/
    ],

    'pexels' => [
        'key' => env('PEXELS_API_KEY'),
        // Get your free API key at: https://www.pexels.com/api/
    ],

    'unsplash' => [
        'key' => env('UNSPLASH_ACCESS_KEY'),
        // Get your free API key at: https://unsplash.com/developers
    ],

];
