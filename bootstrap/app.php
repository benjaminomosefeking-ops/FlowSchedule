<?php

use App\Http\Middleware\LogSessionId;
use App\Http\Middleware\SecureHeadersMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Global: no necesita sesión, está bien aquí
        $middleware->append(SecureHeadersMiddleware::class);
        $middleware->trustProxies(at: '*');

        // Al final del grupo web => StartSession ya se ejecutó
        $middleware->appendToGroup('web', \App\Http\Middleware\LogSessionId::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn(Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })
    ->create();

return $app;
