<?php

namespace App\Controllers;

use RWFramework\Framework\Controller\AbstractController;
use RWFramework\Framework\Http\Response;

class RegistrationController extends AbstractController
{
    public function index(): Response
    {
        return $this->render('register.html.twig');
    }

    public function register(): Response
    {
        dd($this->request);
    }
}