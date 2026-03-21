<?php

$centralHost = env('CENTRAL_DOMAIN', '127.0.0.1');
$centralPort = (int) env('CENTRAL_PORT', 8000);

return [
    'current_version' => env('APP_RELEASE_VERSION', '1.0.0'),

    'latest_version' => env('CENTRAL_UPDATE_VERSION', env('APP_RELEASE_VERSION', '1.0.0')),

    'release_notes' => env('CENTRAL_UPDATE_NOTES', 'New improvements and fixes are available.'),

    'published_at' => env('CENTRAL_UPDATE_PUBLISHED_AT'),

    'package_filename' => env('CENTRAL_UPDATE_PACKAGE_FILENAME', 'latest-update.zip'),

    'channel_token' => env('CENTRAL_UPDATE_CHANNEL_TOKEN'),

    'central_base_url' => env('CENTRAL_UPDATE_BASE_URL', "http://{$centralHost}:{$centralPort}"),
];
