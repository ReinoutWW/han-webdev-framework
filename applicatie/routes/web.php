<?php

use App\Controllers\HomeController;
use App\Controllers\PostsController;
use RWFramework\Framework\Http\Response;

return [
    ['GET', '/', [HomeController::class, 'index']],
    ['GET', '/posts/{id:\d+}', [PostsController::class, 'show']],
    ['GET', '/posts', [PostsController::class, 'create']],
    ['POST', '/posts', [PostsController::class, 'store']],
    ['GET', '/hello/{name:.+}', function(string $name) { // Inline callback function
        return new Response("Hello $name");
    }],
    ['GET', '/register', [\App\Controllers\RegistrationController::class, 'index']],
    ['POST', '/register', [\App\Controllers\RegistrationController::class, 'register']],
    ['GET', '/login', [\App\Controllers\LoginController::class, 'index']],
    ['POST', '/login', [\App\Controllers\LoginController::class, 'login']],
    ['GET', '/dashboard', [\App\Controllers\DashboardController::class, 'index']],
];