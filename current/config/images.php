<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Auto Image Fetching
    |--------------------------------------------------------------------------
    |
    | Enable/disable automatic image fetching for products
    |
    */
    'auto_fetch' => env('IMAGES_AUTO_FETCH', true),

    /*
    |--------------------------------------------------------------------------
    | Image Source
    |--------------------------------------------------------------------------
    |
    | Source for auto images:
    |   - 'pixabay' (recommended, free, 100 req/min)
    |   - 'pexels' (requires API key, good quality)
    |   - 'unsplash' (requires API key, artistic)
    |   - 'placeholder' (fallback, no real images)
    |
    | Get API keys:
    |   Pixabay: https://pixabay.com/api/docs/
    |   Pexels: https://www.pexels.com/api/
    |   Unsplash: https://unsplash.com/developers
    |
    */
    'source' => env('IMAGES_SOURCE', 'pixabay'),

    /*
    |--------------------------------------------------------------------------
    | Default Image
    |--------------------------------------------------------------------------
    |
    | Default image when no other image is available
    |
    */
    'default' => env('IMAGES_DEFAULT', '/images/producto-default.svg'),

    /*
    |--------------------------------------------------------------------------
    | Cache Duration
    |--------------------------------------------------------------------------
    |
    | Hours to cache auto-fetched images
    |
    */
    'cache_hours' => env('IMAGES_CACHE_HOURS', 24),
];
