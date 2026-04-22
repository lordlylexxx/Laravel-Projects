<?php

$centralHost = env('CENTRAL_DOMAIN', '127.0.0.1');
$centralPort = (int) env('CENTRAL_PORT', 8000);

return [
    'current_version' => env('APP_RELEASE_VERSION', '1.0.0'),

    'latest_version' => env('CENTRAL_UPDATE_VERSION', env('APP_RELEASE_VERSION', '1.0.0')),

    'release_notes' => env('CENTRAL_UPDATE_NOTES', 'New improvements and fixes are available.'),

    'published_at' => env('CENTRAL_UPDATE_PUBLISHED_AT'),

    'package_filename' => env('CENTRAL_UPDATE_PACKAGE_FILENAME', 'latest-update.zip'),

    'checksum_filename' => env('CENTRAL_UPDATE_CHECKSUM_FILENAME', ''),

    'channel_token' => env('CENTRAL_UPDATE_CHANNEL_TOKEN'),

    'central_base_url' => env('CENTRAL_UPDATE_BASE_URL', "http://{$centralHost}:{$centralPort}"),

    /*
    | Optional: fetch latest_version, release_notes, published_at from GitHub Releases API
    | (central app only). Repo format: "owner/name". Empty = use env keys above only.
    */
    'github_repo' => env('CENTRAL_GITHUB_REPO', ''),

    /*
    | Optional: pick a specific release asset by filename (substring match if not exact).
    | If unset, the first .zip asset is used; if there are no assets, the tag source archive is used.
    */
    'github_release_asset' => env('CENTRAL_GITHUB_RELEASE_ASSET', ''),

    'github_release_checksum_asset' => env('CENTRAL_GITHUB_RELEASE_CHECKSUM_ASSET', ''),

    'github_token' => env('CENTRAL_GITHUB_TOKEN', ''),

    'github_cache_ttl' => (int) env('CENTRAL_GITHUB_CACHE_TTL', 300),

    'regenerate_app_key' => (bool) env('UPDATE_REGENERATE_APP_KEY', false),

    'require_download_checksum' => (bool) env('UPDATE_REQUIRE_DOWNLOAD_CHECKSUM', false),

    'trusted_download_hosts' => array_values(array_filter(array_map('trim', explode(',', (string) env('UPDATE_TRUSTED_DOWNLOAD_HOSTS', 'github.com,objects.githubusercontent.com,githubusercontent.com'))))),
];
