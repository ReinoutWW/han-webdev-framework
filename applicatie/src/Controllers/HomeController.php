<?php

namespace App\Controllers;

use App\Widget;
use RWFramework\Framework\Controller\AbstractController;
use RWFramework\Framework\Http\Response;

class HomeController extends AbstractController {
    public function __construct(private Widget $widget) {
    }

    public function index(): Response {

        return $this->render("home.html.twig");
    }
}