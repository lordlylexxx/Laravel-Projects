<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->validateCsrfTokens(except: [
            'logout',
        ]);

        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'role.access' => \App\Http\Middleware\RoleAccessMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            'client' => \App\Http\Middleware\EnsureUserIsClient::class,
            'owner' => \App\Http\Middleware\EnsureUserIsOwner::class,
            'owner.onboarded' => \App\Http\Middleware\EnsureOwnerOnboardingComplete::class,
            'admin' => \App\Http\Middleware\EnsureUserIsAdmin::class,
            'tenant.manager' => \App\Http\Middleware\EnsureUserIsOwnerOrTenantAdmin::class,
            'tenant.context' => \App\Http\Middleware\SetCurrentTenant::class,
            'tenant.required' => \Spatie\Multitenancy\Http\Middleware\NeedsTenant::class,
            'tenant.session' => \App\Http\Middleware\EnsureTenantSessionIsSynchronized::class,
            'tenant.active' => \App\Http\Middleware\EnsureTenantIsActive::class,
            'tenant.bandwidth' => \App\Http\Middleware\RecordTenantBandwidthUsage::class,
            'central.port' => \App\Http\Middleware\EnsureRequestUsesCentralPort::class,
            'tenant.port' => \App\Http\Middleware\EnsureRequestUsesTenantPort::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
