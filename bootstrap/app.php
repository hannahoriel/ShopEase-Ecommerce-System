<?php

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
    // Laravel's default auth redirect points to /auth/login.  Keep the
    // Logistics, Sorting Center and Delivery Hub entry flow on its own landing.
    $middleware->redirectGuestsTo(function (Request $request): string {
        if ($request->is('logistics', 'logistics/*', 'sorting-center', 'sorting-center/*', 'delivery-hub', 'delivery-hub/*')) {
            return route('logistics.landing.info');
        }

        return route('landing.page', ['auth' => 'login']);
    });
    $middleware->statefulApi();
    $middleware->web(append: [
        \App\Http\Middleware\ShopEaseLoading::class,
        \App\Http\Middleware\AdminModuleAccess::class,
    ]);
    $middleware->api(append: [
        \App\Http\Middleware\AdminModuleAccess::class,
    ]);
    $middleware->alias([
        'role' => \App\Http\Middleware\EnsureUserHasRole::class,
    ]);
})
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
