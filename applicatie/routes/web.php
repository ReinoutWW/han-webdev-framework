<?php

use App\Controllers\HomeController;
use App\Controllers\PostsController;
use RWFramework\Framework\Http\Response;

return [
    ['GET', '/', [HomeController::class, 'index']],
    ['POST', '/post', [PostsController::class, 'index']],
    ['GET', '/posts/{id:\d+}', [PostsController::class, 'show']],
    ['GET', '/hello/{name:.+}', function(string $name) { // Inline callback function
        return new Response("Hello $name");
    }]
];