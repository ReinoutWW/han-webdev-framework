<?php

namespace App\Controllers;

use RWFramework\Framework\Controller\AbstractController;
use RWFramework\Framework\Http\Response;

class DashboardController extends AbstractController {
    public function index(): Response {
        return $this->render('dashboard.html.twig');
    }
}