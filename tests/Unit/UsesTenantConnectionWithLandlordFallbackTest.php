<?php

use App\Models\Concerns\UsesTenantConnectionWithLandlordFallback;
use Illuminate\Http\Request;

uses(Tests\TestCase::class);

test('tenant host is detected even when app instance is central', function () {
    $resolver = new class
    {
        use UsesTenantConnectionWithLandlordFallback;
    };

    $previousInstance = getenv('APP_INSTANCE');
    putenv('APP_INSTANCE=central');

    app()->instance('request', Request::create('http://tenant-a.example.test/register'));

    $method = new ReflectionMethod($resolver, 'isTenantAppRequest');
    $method->setAccessible(true);

    try {
        expect($method->invoke($resolver))->toBeTrue();
    } finally {
        if ($previousInstance === false) {
            putenv('APP_INSTANCE');
        } else {
            putenv('APP_INSTANCE='.$previousInstance);
        }
    }
});

test('central host is not detected as tenant request', function () {
    $resolver = new class
    {
        use UsesTenantConnectionWithLandlordFallback;
    };

    app()->instance('request', Request::create('http://localhost/register'));

    $method = new ReflectionMethod($resolver, 'isTenantAppRequest');
    $method->setAccessible(true);

    expect($method->invoke($resolver))->toBeFalse();
});
