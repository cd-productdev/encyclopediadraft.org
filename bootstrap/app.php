<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'jwt' => \App\Http\Middleware\JwtAuthMiddleware::class,
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'update.lastactive' => \App\Http\Middleware\UpdateLastActive::class,
        ]);

        // Add UpdateLastActive middleware to API routes
        $middleware->api(append: [
            \App\Http\Middleware\UpdateLastActive::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
