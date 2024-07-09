<?php

use Illuminate\Foundation\Application;
use App\Http\Middleware\HandleCorsHeaders;

use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Laravel\Sanctum\Http\Middleware\CheckAbilities;
use Laravel\Sanctum\Http\Middleware\CheckForAnyAbility;
use App\Http\Middleware\HandleMemory;
use App\Http\Middleware\HandleCorsSanctum;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) { //sanctum abilities middleware
        $middleware->alias([
            'abilities' => CheckAbilities::class,
            'ability' => CheckForAnyAbility::class,
            //'handlememory' => HandleMemory::class,
            'cors' => HandleCorsHeaders::class, // \Illuminate\Http\Middleware\HandleCors::class,
        ]);
        $middleware->append(HandleCorsHeaders::class);
        $middleware->append(HandleCorsSanctum::class); //$middleware->append(\Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class);
        $middleware->append(HandleMemory::class);
        //$middleware->append(\Illuminate\Session\Middleware\StartSession::class);

        // $middleware->api(prepend: [
        //     \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class,
        // ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();

