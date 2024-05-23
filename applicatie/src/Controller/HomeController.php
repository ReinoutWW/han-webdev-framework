<?php

namespace App\Controller;

use RWFramework\Framework\Http\Response;

class HomeController {
    public function index(): Response {
        $content = '<h1>Welcome to the homepage!</h1>';

        return new Response($content);
    }
}