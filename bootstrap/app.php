<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use App\Http\Middleware\AdminAuthenticate;
use App\Http\Middleware\FreelancerMiddleware;
use App\Http\Middleware\SetDashboardLocale;
use App\Http\Middleware\SetLocaleFromHeader;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'admin' => AdminAuthenticate::class,
            'freelancer' => FreelancerMiddleware::class,
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            'check.blocked' => \App\Http\Middleware\CheckBlocked::class,
            'owns'       => \App\Http\Middleware\OwnsResource::class,
            'freelancer.api' => \App\Http\Middleware\ApiFreelancerMiddleware::class,



        ]);
        $middleware->web(append: [
            SetDashboardLocale::class,
        ]);
        $middleware->api(append: [
            SetLocaleFromHeader::class,
            \App\Http\Middleware\CurrencyMiddleware::class,
        ]);
        $middleware->validateCsrfTokens(except: [
            'stripe/*',
        ]);
    })
    ->withRouting(
        using: function () {
            Route::middleware('web')
                ->group(base_path('routes/web.php'));

            Route::middleware('api')
                ->prefix('api/v1')
                ->group(base_path('routes/v1/api.php'));
        },

    )               
    // ->withExceptions(function (Exceptions $exceptions) {
    //     //
    // })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (ValidationException $e, $request) {
            // Only apply to API requests
            if ($request->expectsJson() || $request->is('api/*')) {

                $messages = collect($e->errors())
                    ->flatten()
                    ->unique()
                    ->values();

                return response()->json([
                    'message' => $messages->implode(' & '),
                    'errors'  => $e->errors(),
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            return null;
        });
    })


    ->create();
