<?php

declare(strict_types=1);

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (DomainException $exception, Request $request) {
            if (! method_exists($exception, 'getErrorCode') || ! method_exists($exception, 'getHttpStatus')) {
                return null;
            }

            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'data' => null,
                    'message' => $exception->getMessage(),
                    'code' => $exception->getErrorCode(),
                    'status_code' => $exception->getHttpStatus(),
                    'errors' => [],
                ], $exception->getHttpStatus());
            }
        });
    })->create();
