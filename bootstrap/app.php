<?php

use App\Http\Middleware\RoleMiddleware;
use App\Http\Middleware\SanitizeInput;
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
        // Di belakang proxy Railway/hosting cloud: percayai header X-Forwarded-*
        // supaya URL dan HTTPS terdeteksi benar.
        $middleware->trustProxies(at: '*');

        // Alias middleware RBAC: dipakai sebagai 'role:admin' di routes/web.php
        $middleware->alias([
            'role' => RoleMiddleware::class,
        ]);

        // Sanitasi input dijalankan untuk setiap request web.
        $middleware->web(append: [
            SanitizeInput::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
