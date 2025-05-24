<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\RedirectIfAuthenticated;
use App\Http\Middleware\AdminMiddleware;
use Illuminate\Session\Middleware\StartSession;
use App\Http\Middleware\UpdateLastActiveAt;
use App\Http\Middleware\TrustProxies;
use App\Http\Middleware\CheckRole;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        // api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Aliases
        $middleware->alias([
            'guest' => RedirectIfAuthenticated::class,
            'admin' => AdminMiddleware::class,
            'role' => CheckRole::class,
        ]);

        // Thêm middleware cho nhóm 'web'
        $middleware->appendToGroup('web', [
            StartSession::class,
            UpdateLastActiveAt::class,
        ]);

        // ✅ Thêm TrustProxies vào toàn cục
        $middleware->append([
            TrustProxies::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->create();
