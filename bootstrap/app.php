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
        $middleware->alias([
            'must.change.password' => \App\Http\Middleware\MustChangePassword::class,
            'no.cache' => \App\Http\Middleware\NoCacheAuthenticated::class,
            // D109/D113/D117 — subdomain tenant resolution + session bridge.
            'tenant.resolve' => \Stancl\Tenancy\Middleware\InitializeTenancyBySubdomain::class,
            'tenant.session' => \App\Http\Middleware\WriteTenantSessionFromTenancy::class,
            // Symmetric host partitioning: tenant routes only on tenant subdomains;
            // admin routes only on admin host.
            'tenant.only' => \Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains::class,
            'only.on.domain' => \App\Http\Middleware\OnlyOnDomain::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Friendly 404 for unknown tenant subdomains, not stancl's exception page.
        $exceptions->render(function (\Stancl\Tenancy\Exceptions\TenantCouldNotBeIdentifiedException $e) {
            return response()->view('errors.tenant-not-found', [], 404);
        });
    })->create();