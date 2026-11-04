<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Percayai seluruh reverse proxy / Cloudflare Tunnel agar header X-Forwarded-Proto (HTTPS) terbaca akurat
        $middleware->trustProxies(at: '*');

        // Pastikan cookie session bisa bekerja baik di HTTPS tunnel maupun HTTP biasa
        $middleware->encryptCookies(except: []);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
