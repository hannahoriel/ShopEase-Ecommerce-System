<?php

return [

    /*
    |--------------------------------------------------------------------|
    | Philippine Location Data Sources
    |--------------------------------------------------------------------|
    | Base URLs for the upstream Philippine location APIs. "psgc" is
    | tried first; "buonzz" is used as a fallback if "psgc" fails.
    | Override with env vars if you ever need to point somewhere else.
    |--------------------------------------------------------------------|
    */

    'base_urls' => [
        'psgc'   => env('PSGC_API_BASE_URL', 'https://psgc.gitlab.io/api'),
        'buonzz' => env('BUONZZ_API_BASE_URL', 'https://ph-locations-api.buonzz.com/v1'),
    ],

    // Order in which upstream sources are attempted.
    'fallback_order' => ['psgc', 'buonzz'],

    // Region code used for Metro Manila / NCR (which has no "province" of its own).
    'ncr_code' => '130000000',

    // How long (in seconds) to cache upstream responses. Location data
    // barely ever changes, so a long cache is safe and keeps the app fast.
    'cache_ttl' => env('LOCATIONS_CACHE_TTL', 60 * 60 * 24), // 24 hours

    // Local PHP installations may not have the current CA bundle. Set
    // LOCATIONS_VERIFY_SSL=true in production when a valid CA bundle exists.
    'verify_ssl' => env('LOCATIONS_VERIFY_SSL', false),

    // Request timeout (seconds) for each upstream call.
    'timeout' => env('LOCATIONS_HTTP_TIMEOUT', 5),
];
