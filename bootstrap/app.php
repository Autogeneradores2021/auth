<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

$apiRoutes = [];
foreach (glob(__DIR__ . '/../routes/Apis/**/*.php') as $routeFile) {
    $basename = basename($routeFile);
    if (!in_array($basename, ['web.php', 'console.php'])) {
        $apiRoutes[] = $routeFile;
    }
}

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: $apiRoutes,
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'auth.api' => \App\Http\Middleware\ApiAuthMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
