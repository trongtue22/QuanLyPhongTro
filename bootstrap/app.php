<?php

use App\Http\Middleware\CheckLogin;
use App\Http\Middleware\PivotDichVu;
use App\Http\Middleware\CheckApiToken;
use App\Http\Middleware\CheckChuTroLogin;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )

    ->withMiddleware(function (Middleware $middleware) {
        
        // Đăng kí các middleware
        $middleware->alias([
            'checklogin' => CheckLogin::class,
            'PivotDichVu' => PivotDichVu::class,
            'CheckApiToken' => CheckApiToken::class,
            'CheckChuTroLogin' => CheckChuTroLogin::class,
        ]);

        

    })


    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
