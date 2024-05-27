<?php

namespace App\Controllers;

use App\Entity\Post;
use App\Repository\PostMapper;
use App\Repository\PostRepository;
use RWFramework\Framework\Controller\AbstractController;
use RWFramework\Framework\Http\RedirectResponse;
use RWFramework\Framework\Http\Response;
use RWFramework\Framework\Session\SessionInterface;

class PostsController extends AbstractController {
    public function __construct(
        private PostMapper $postMapper,
        private PostRepository $postRepository,
        private SessionInterface $session
        ) {

    }

    public function show(int $id): Response {
        $post = $this->postRepository->findOrFail($id);
        
        return $this->render("posts.html.twig", [
            'post' => $post
        ]);
    }

    public function create(): Response {
        return $this->render("create-post.html.twig");
    }

    /**
     * This uses the "score" naming convention. We will store the data in the database
     * But first, we encapsulate the data in a class. This is a DTO (Data Transfer Object)
     */
    public function store(): Response {
        $title = $this->request->postParams["title"];
        $body = $this->request->postParams["body"];

        // Using a named constructor
        $post = Post::create($title, $body);

        $this->postMapper->save($post);

        $this->session->setFlash('success', sprintf('Post "%s" created successfully!', $post->getTitle()));

        return new RedirectResponse('/posts');
    }
}