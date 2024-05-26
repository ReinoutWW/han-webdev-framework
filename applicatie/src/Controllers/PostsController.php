<?php

namespace App\Controllers;

use App\Entity\Post;
use App\Repository\PostMapper;
use RWFramework\Framework\Controller\AbstractController;
use RWFramework\Framework\Http\Response;

class PostsController extends AbstractController {
    public function __construct(private PostMapper $postMapper) {

    }

    public function show(int $id): Response {
        return $this->render("posts.html.twig", [
            'postId' => $id
        ]);
    }

    public function create(): Response {
        return $this->render("create-post.html.twig");
    }

    /**
     * This uses the "score" naming convention. We will store the data in the database
     * But first, we encapsulate the data in a class. This is a DTO (Data Transfer Object)
     */
    public function store(): Void {
        $title = $this->request->postParams["title"];
        $body = $this->request->postParams["body"];

        // Using a named constructor
        $post = Post::create($title, $body);

        $this->postMapper->save($post);
        
        dd($post);
    }
}