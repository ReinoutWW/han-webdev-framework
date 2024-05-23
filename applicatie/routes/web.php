<?php

use App\Controller\HomeController;
use App\Controller\PostsController;
use RWFramework\Framework\Http\Response;

return [
    ['GET', '/', [HomeController::class, 'index']],
    ['POST', '/post', [PostsController::class, 'index']],
    ['GET', '/posts/{id:\d+}', [PostsController::class, 'show']],
    ['GET', '/hello/{name:.+}', function(string $name) { // Inline callback function
        return new Response("Hello $name");
    }]
];