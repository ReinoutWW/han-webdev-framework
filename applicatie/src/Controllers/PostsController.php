<?php

namespace App\Controllers;

use RWFramework\Framework\Http\Response;

class PostsController {
    public function show(int $id): Response {
        $content = '<h1>Welcome to the homepage </h1>' . $id;

        return new Response($content);
    }
}