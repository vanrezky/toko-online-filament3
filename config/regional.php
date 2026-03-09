<?php

return [
    'apicoid' => [
        'key' => env('APICOID_API_KEY'),
        'base_url' => env('APICOID_BASE_URL', 'https://api.co.id'),
    ],

    'export' => [
        'disk' => env('REGIONAL_EXPORT_DISK', 'local'),
        'path' => env('REGIONAL_EXPORT_PATH', 'locations/indonesia'),
        'chunk_size' => env('REGIONAL_EXPORT_CHUNK', 1000),
    ],

    'cache_ttl' => env('REGIONAL_CACHE_TTL', 86400), // Default 24 hours
];
