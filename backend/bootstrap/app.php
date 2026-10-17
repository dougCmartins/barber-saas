<?php

declare(strict_types=1);

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

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
        $exceptions->render(function (ValidationException $exception, Request $request) {
            if (! $request->is('api/*') && ! $request->expectsJson()) {
                return null;
            }

            $errors = [];

            foreach ($exception->validator->failed() as $field => $rules) {
                $rule = array_key_first($rules);
                $errors[$field] = [is_string($rule) ? strtolower($rule) : 'invalid'];
            }

            return response()->json([
                'data' => null,
                'message' => $exception->getMessage(),
                'code' => 'VALIDATION_FAILED',
                'status_code' => $exception->status,
                'errors' => $errors,
            ], $exception->status);
        });

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
