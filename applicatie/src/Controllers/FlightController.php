<?php

namespace App\Controllers;

use App\Entity\Flight;
use App\Repository\AirportRepository;
use App\Repository\Filters\SearchFilters;
use App\Repository\FlightMapper;
use App\Repository\FlightRepository;
use App\Repository\LuggageRepository;
use App\Repository\PassengerRepository;
use RWFramework\Framework\Controller\AbstractController;
use RWFramework\Framework\Http\RedirectResponse;
use RWFramework\Framework\Http\Response;
use RWFramework\Framework\Session\Session;
use RWFramework\Framework\Session\SessionInterface;

class FlightController extends AbstractController
{
    public function __construct(
        private FlightRepository $flightRepository,
        private PassengerRepository $passengerRepository,
        private FlightMapper $flightMapper,
        private SessionInterface $session,
        private LuggageRepository $luggageRepository,
        private AirportRepository $airportRepository
    ) { }

    public function index()
    {
        // Get all flights from a specific page (default = 1)
        $page = $this->request->getParam('page') ?? 1;
        $flightsPage = ($page > 0) ? $page : 1;
        $allowedFilters = [
            'vluchtnummer',
            'bestemming',
            'vertrektijd'
        ];

        $filters = SearchFilters::parseArrayToFilters($this->request->getParams(), $allowedFilters);

        // Get flights from Repository
        $flights = $this->flightRepository->getFlights($flightsPage, $filters);

        $airports = $this->airportRepository->getAirports();

        // Return the view with the flights
        return $this->render("Flight/flight.html.twig", [
            'flights' => $flights,
            'page' => $page,
            'airports'=> $airports
        ]);
    }

    public function flight(int $flightNumber, int $userId = null) {
        // If null, get user from session (If an employee loads the page, the userId is not null)
        if($userId === null) {
            $userId = $this->request->getSession()->getUser()->getId();
        }        

        // Get the flight from the Repository
        $flight = $this->flightRepository->findByFlightNumber($flightNumber);

        // If employee watches the flight, get the passenger details
        $passengers = $this->passengerRepository->getPassengersByFlightNumber($flightNumber);

        // If passenger watches the flight, get the personal passenger details
        $passengerDetails = $this->passengerRepository->getBookedFlightPassengerDetails($userId, $flightNumber);

        $passengerLuggage = null;
        if($passengerDetails) {
            $passengerLuggage = $this->luggageRepository->getLuggage($flightNumber, $passengerDetails->getPassengerNumber());
        }

        // Return the view with the flight
        return $this->render("Flight/flight-detail.html.twig", [
            'flight' => $flight,
            'passenger_details' => $passengerDetails,
            'passengers' => $passengers,
            'enableFrontPlane' => true,
            'passengerLuggage' => $passengerLuggage
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
}