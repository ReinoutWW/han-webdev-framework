<?php

namespace App\Controllers;
use RWFramework\Framework\Controller\AbstractController;

class FlightController extends AbstractController
{
    public function index()
    {
        return $this->render('flight.html.twig');
    }
}