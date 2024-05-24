<?php

namespace App\Controllers;

use RWFramework\Framework\Controller\AbstractController;
use RWFramework\Framework\Http\Response;

class PostsController extends AbstractController {
    public function show(int $id): Response {
        return $this->render("posts.html.twig", [
            'postId' => $id
        ]);
    }
}