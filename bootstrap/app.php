<?php

use App\Models\Tenant;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->prependToGroup('web', \App\Http\Middleware\ForceRootUrlWhenRequestHostDiffersFromAppUrl::class);

        $middleware->validateCsrfTokens(except: [
            'logout',
            'stripe/webhook',
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
            'tenant.permissions_team' => \App\Http\Middleware\SetSpatiePermissionsTeamForTenant::class,
            'tenant.client_guest_rbac' => \App\Http\Middleware\EnsureTenantClientGuestRbacOnAuth::class,
            'tenant.session' => \App\Http\Middleware\EnsureTenantSessionIsSynchronized::class,
            'tenant.active' => \App\Http\Middleware\EnsureTenantIsActive::class,
            'tenant.bandwidth' => \App\Http\Middleware\RecordTenantBandwidthUsage::class,
            'central.port' => \App\Http\Middleware\EnsureRequestUsesCentralPort::class,
            'tenant.port' => \App\Http\Middleware\EnsureRequestUsesTenantPort::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Throwable $e, Request $request) {
            if ($request->expectsJson() || ! Tenant::checkCurrent()) {
                return null;
            }

            $statusCode = null;
            if ($e instanceof HttpExceptionInterface) {
                $statusCode = $e->getStatusCode();
            } elseif ($e instanceof AuthorizationException) {
                $statusCode = 403;
            }

            if ($statusCode !== 403) {
                return null;
            }

            return response()->view('tenant.limited-access', [
                'tenant' => Tenant::current(),
                'message' => 'Your role does not currently include access to this page. Contact your business owner/admin if you need this permission.',
            ], 403);
        });
    })
    ->create();
