<?php

return [
    // Prefer new keys, but keep legacy aliases for existing deployments.
    'github_repo' => env('GITHUB_REPO', env('CENTRAL_GITHUB_REPO', '')),
    'github_token' => env('GITHUB_TOKEN', env('GITHUB_API_KEY', env('CENTRAL_GITHUB_TOKEN', ''))),
    // CA bundle used for GitHub HTTPS verification on local Windows/PHP setups.
    'github_ca_bundle' => env('GITHUB_CA_BUNDLE', storage_path('certs/cacert.pem')),
    // Set to false only for local debugging if CA chain cannot be resolved.
    'github_ssl_verify' => env('GITHUB_SSL_VERIFY', true),
];
