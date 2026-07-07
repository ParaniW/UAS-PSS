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
    ->withMiddleware(function (Middleware $middleware) {
        // 1. Mempercayai proxy Railway agar rate limiting aman dari false-positive
        $middleware->trustProxies(at: '*');

        // 2. Mendaftarkan alias middleware kustom 'role' kamu agar bisa dibaca rute web
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class, // <-- Pastikan arah file RoleMiddleware kamu sudah sesuai di folder ini!
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
