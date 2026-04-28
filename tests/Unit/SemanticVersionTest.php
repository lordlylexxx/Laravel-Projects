<?php

use App\Models\AppRelease;
use App\Support\SemanticVersion;

it('picks newest release by semver tag', function () {
    $releases = collect([
        new AppRelease(['tag' => 'v1.0.9-dev']),
        new AppRelease(['tag' => 'v1.0.11-dev']),
        new AppRelease(['tag' => 'v1.0.10']),
    ]);

    $newest = SemanticVersion::newestRelease($releases);

    expect($newest?->tag)->toBe('v1.0.11-dev');
});

it('normalizes v prefix for comparison', function () {
    expect(SemanticVersion::normalize('v2.1.0'))->toBe('2.1.0');
});
