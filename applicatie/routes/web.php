<?php

use App\Controllers\HomeController;
use App\Controllers\PostsController;
use RWFramework\Framework\Routing\Route;

return [
    new Route('GET', '/', [HomeController::class, 'index']),
    new Route('GET', '/posts/{id:\d+}', [PostsController::class, 'show']),
    new Route('GET', '/posts', [PostsController::class, 'create']),
    new Route('POST', '/posts', [PostsController::class, 'store']),
    new Route('POST', '/register', [\App\Controllers\RegistrationController::class, 'register']),
    new Route('GET', '/register', [\App\Controllers\RegistrationController::class, 'index'],
        [
            RWFramework\Framework\Http\Middleware\Guest::class
        ]
    ),
    new Route('POST', '/login', [\App\Controllers\LoginController::class, 'login']),
    new Route('GET', '/logout', [\App\Controllers\LoginController::class, 'logout'],
        [
            RWFramework\Framework\Http\Middleware\Authenticate::class
        ]
    ),
    new Route('GET', '/login', [\App\Controllers\LoginController::class, 'index'],
        [
            RWFramework\Framework\Http\Middleware\Guest::class
        ]
    ),
    new Route('GET', '/dashboard', [\App\Controllers\DashboardController::class, 'index'], 
        [  
            RWFramework\Framework\Http\Middleware\Authenticate::class,
            RWFramework\Framework\Http\Middleware\Dummy::class
        ]
    )
];