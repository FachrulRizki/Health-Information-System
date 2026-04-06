<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Force HTTPS in production (Req 20.2)
        $middleware->prepend(\App\Http\Middleware\ForceHttpsMiddleware::class);

        $middleware->alias([
            'permission' => \App\Http\Middleware\PermissionMiddleware::class,
            'session.timeout' => \App\Http\Middleware\SessionTimeoutMiddleware::class,
            'audit' => \App\Http\Middleware\AuditTrailMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
