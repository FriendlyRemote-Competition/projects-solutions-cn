<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
        $exceptions->render(function (Exception $e, \Illuminate\Http\Request $request) {
            if ($request->ajax() || $request->wantsJson()) {
                if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpException) {
                    return response()->json([
                        "message" => match ($e->getStatusCode()) {
                            401 => "Unauthenticated",
                            403 => "Forbidden",
                            404 => "Resource not found",
                            default => $e->getMessage(),
                        }
                    ], $e->getStatusCode());
                }

                if ($e instanceof \Illuminate\Validation\ValidationException) {
                    $errors = [];
                    foreach ($e->errors() as $key => $value) {
                        $errors[$key] = $value[0];
                    }
                    return response()->json([
                        "message" => "Validation failed",
                        "errors" => $errors,
                    ], 422);
                }
            }
        });
    })->create();
