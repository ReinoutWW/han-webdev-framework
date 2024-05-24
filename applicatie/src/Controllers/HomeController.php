<?php

namespace App\Controllers;

use App\Widget;
use RWFramework\Framework\Controller\AbstractController;
use RWFramework\Framework\Http\Response;

class HomeController extends AbstractController {
    public function __construct(private Widget $widget) {
    }

    public function index(): Response {
        dd($this->container->get('twig'));

        $content = "<h1>Hello {$this->widget->name}</h1>";
        return new Response($content);
    }
}