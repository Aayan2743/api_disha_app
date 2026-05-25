<?php

use App\Http\Middleware\JwtAuthMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {

        $middleware->alias([
            'jwt.api' => JwtAuthMiddleware::class,
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions): void {

        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, $request) {

            if ($request->is('api/*')) {

                return response()->json([
                    'success' => false,
                    'error'   => 'UNAUTHENTICATED',
                    'message' => 'Invalid or missing token',
                ], 401);
            }
        });

        $exceptions->render(function (\Throwable $e, $request) {

            if (! $request->is('api/*')) {
                return null;
            }

            if ($e instanceof HttpExceptionInterface) {

                return response()->json([
                    'success' => false,
                    'error'   => 'HTTP_ERROR',
                    'message' => $e->getMessage(),
                ], $e->getStatusCode());
            }

            return response()->json([
                'success' => false,
                'error'   => 'SERVER_ERROR',
                'message' => app()->isProduction()
                    ? 'Something went wrong'
                    : $e->getMessage(),
            ], 500);
        });

    })
    ->create();
