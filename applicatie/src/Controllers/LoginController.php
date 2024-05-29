<?php

namespace App\Controllers;

use RWFramework\Framework\Controller\AbstractController;
use RWFramework\Framework\Http\Response;

class LoginController extends AbstractController {
    public function index(): Response {
        return $this->render('login.html.twig');
    }

    public function login(): Response {
        dd($this->request);
    }
}