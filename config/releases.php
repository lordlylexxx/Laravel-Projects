<?php

return [
    /*
    |--------------------------------------------------------------------------
    | GitHub repository for release sync
    |--------------------------------------------------------------------------
    |
    | Use "owner/repository". GITHUB_REPO is preferred; CENTRAL_GITHUB_REPO is
    | accepted for compatibility with central app documentation.
    |
    */
    'github_repo' => env('GITHUB_REPO', env('CENTRAL_GITHUB_REPO', '')),

    'github_token' => env('GITHUB_TOKEN', env('GITHUB_API_KEY', env('CENTRAL_GITHUB_TOKEN', ''))),

    'github_ca_bundle' => env('GITHUB_CA_BUNDLE', storage_path('certs/cacert.pem')),

    'github_ssl_verify' => env('GITHUB_SSL_VERIFY', true),

    'github_releases_per_page' => min(100, max(1, (int) env('GITHUB_RELEASES_PER_PAGE', 100))),

    /*
    | When false, only non-prerelease GitHub releases are offered as available
    | updates on tenant dashboards. Enable if you publish pre-releases only.
    */
    'offer_prereleases_to_tenants' => env('RELEASES_OFFER_PRERELEASES_TO_TENANTS', false),

    /*
    | Import git tags that do not have a GitHub "Release" object (GET /releases can be empty
    | while /tags lists versions). Disable if you only want formal Releases.
    */
    'sync_git_tags' => env('RELEASES_SYNC_GIT_TAGS', true),

    /*
    | After a tenant applies an update, run tenant migrations then re-sync RBAC from code
    | (TenantRbacSeeder) so new permissions/roles from the release take effect.
    */
    'sync_rbac_after_tenant_apply' => env('RELEASES_SYNC_RBAC_AFTER_TENANT_APPLY', true),
];
