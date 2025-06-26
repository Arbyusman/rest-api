<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\Http\Middleware\CheckAbilities;
use Laravel\Sanctum\Http\Middleware\CheckForAnyAbility;
use Symfony\Component\HttpKernel\Exception\HttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'abilities' => CheckAbilities::class,
            'ability' => CheckForAnyAbility::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->shouldRenderJsonWhen(function (Request $request, Throwable $e) {
            return $request->is('api/*') || $request->expectsJson();
        });

        $exceptions->render(function (ValidationException $e) {
            return response()->json([
                'metadata' => [
                    'status' => 422,
                    'message' => 'Validation Error',
                    'error' => $e->errors(),
                ],
            ], 422);
        });

        $exceptions->render(function (Throwable $e) {
            return response()->json([
                'metadata' => [
                    'status' => $e instanceof HttpException ? $e->getStatusCode() : 500,
                    'message' => $e->getMessage() ?: 'Server Error',
                ],
            ], $e instanceof HttpException ? $e->getStatusCode() : 500);
        });
    })
    ->create();
