<?php

namespace App\Controllers;

use App\Entity\Flight;
use App\Repository\flightMapper;
use App\Repository\FlightRepository;
use RWFramework\Framework\Controller\AbstractController;
use RWFramework\Framework\Http\RedirectResponse;
use RWFramework\Framework\Http\Response;
use RWFramework\Framework\Session\Session;

class FlightController extends AbstractController
{
    public function __construct(
        private FlightRepository $flightRepository,
        private flightMapper $flightMapper
    ) { }

    public function index()
    {
        // Get all flights from a specific page (default = 1)
        $page = $this->request->getParam('page') ?? 1;
        $flightsPage = ($page > 0) ? $page : 1;

        // Get flights from Repository
        $flights = $this->flightRepository->getFlights($flightsPage);

        // Return the view with the flights
        return $this->render("Flight/flight.html.twig", [
            'flights' => $flights,
            'page' => $page
        ]);
    }

    public function flight(int $flightNumber) {
        // Get the flight from the Repository
        $flight = $this->flightRepository->findByFlightNumber($flightNumber);

        // Return the view with the flight
        return $this->render("Flight/flight-detail.html.twig", [
            'flight' => $flight
        ]);
    }

    public function create(): Response {
        return $this->render("Flight/create-flight.html.twig");
    }

    public function store(): Response {
        $flight = Flight::create(
            $this->request->input('flightnumber'),
            $this->request->input('destination'),
            $this->request->input('gate'),
            $this->request->input('maxpassengers'),
            $this->request->input('maxweightpp'),
            $this->request->input('maxtotalweight'),
            new \DateTimeImmutable($this->request->input('departuretime')),
            $this->request->input('airlinecode'),
        );

        $form = new \App\Form\Flight\CreateFlightForm($this->flightMapper);

        $form->setFlight($flight);

        if($form->hasValidationErrors()) {
            foreach($form->getValidationErrors() as $error) {
                $this->request->getSession()->setFlash('error', $error);
            }

            return new RedirectResponse('/vluchten/nieuw');
        }

        // Register flight
        $form->save();

        $this->request->getSession()->setFlash(Session::NOTIFICATION_SUCCESS, sprintf('Vlucht "%s" is succesvol aangemaakt!', $flight->getFlightNumber()));

        return new RedirectResponse('/vluchten/nieuw');
    }

    public function book(int $flightNumber) {
        dd('Book flight' . $flightNumber);
    }
}