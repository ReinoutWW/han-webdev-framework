<?php

use App\Controllers\HomeController;
use App\Controllers\PostsController;
use RWFramework\Framework\Http\Response;

return [
    ['GET', '/', [HomeController::class, 'index']],
    ['POST', '/posts', [PostsController::class, 'index']],
    ['GET', '/posts', [PostsController::class, 'create']],
    ['GET', '/hello/{name:.+}', function(string $name) { // Inline callback function
        return new Response("Hello $name");
    }]
];