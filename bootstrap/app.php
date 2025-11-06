<?php

use App\Http\Middleware\AuthApiMiddleware;
use App\Http\Middleware\CheckModuleAccess;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Client\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Add Middleware API
        $middleware->alias([
            'checkAccess' => CheckModuleAccess::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Add Exception For API

    })->create();
