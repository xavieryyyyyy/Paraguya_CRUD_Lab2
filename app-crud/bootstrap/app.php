<?php

// Application setup. Registers routes/web.php, routes/console.php, and the health endpoint.
// Laravel supplies its default middleware; named auth/guest middleware is applied in the route
// files.


use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // No additional code is needed in this generated section for the CRUD lab.
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // No additional code is needed in this generated section for the CRUD lab.
    })->create();
