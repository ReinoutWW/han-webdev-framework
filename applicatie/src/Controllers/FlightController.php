<?php

namespace App\Controllers;

use App\Entity\Flight;
use App\Repository\flightMapper;
use App\Repository\FlightRepository;
use App\Repository\PassengerRepository;
use RWFramework\Framework\Controller\AbstractController;
use RWFramework\Framework\Http\RedirectResponse;
use RWFramework\Framework\Http\Response;
use RWFramework\Framework\Session\Session;

class FlightController extends AbstractController
{
    public function __construct(
        private FlightRepository $flightRepository,
        private PassengerRepository $passengerRepository,
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

        // Return the view with the flight
        return $this->render("Flight/flight-detail.html.twig", [
            'flight' => $flight,
            'passenger_details' => $passengerDetails,
            'passengers' => $passengers,
            'enableFrontPlane' => true
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

    public function seatSelection(int $flightNumber) {
        $passengers = $this->passengerRepository->getPassengersByFlightNumber($flightNumber);

        $flight = $this->flightRepository->findByFlightNumber($flightNumber);

        return $this->render("Flight/seat-selection.html.twig", [
            'passengers' => $passengers,
            'flight' => $flight,
            'bookSeatOption' => true
        ]);
    } 

    public function book(int $flightNumber) {
        // Get user from session
        $user = $this->request->getSession()->getUser();

        // Check if there's space available on the flight
        $flightSeatsFull = $this->flightRepository->areSeatsFullByFlightNumber($flightNumber);
        
        if($flightSeatsFull) {
            $this->request->getSession()->setFlash(Session::NOTIFICATION_ERROR, 'Er zijn geen stoelen meer beschikbaar op deze vlucht.');
            return new RedirectResponse('/vluchten/' . $flightNumber);
        }

        // Get seat number
        $seat = $this->request->input('seat');

        $isSeatAvailable = $this->flightRepository->isSeatAvailable($flightNumber, $seat);

        if(!$isSeatAvailable) {
            $this->request->getSession()->setFlash(Session::NOTIFICATION_ERROR, 'Deze stoel is reeds bezet. Kies een andere stoel. Excuses voor het ongemak.');
            return new RedirectResponse('/stoel_boeken/' . $flightNumber);
        }

        // If the seat is not set, get the next available seat
        $seatNumber = $seat ?? $this->flightRepository->getNextAvailableSeatByFlightNumber($flightNumber);

        $this->passengerRepository->addToFlight($user, $flightNumber, $seatNumber);

        // Show success message
        $this->request->getSession()->setFlash(Session::NOTIFICATION_SUCCESS, 'Uw stoel is succesvol geboekt!');

        return new RedirectResponse('/vluchten/' . $flightNumber);
    }

    public function booked() {
        // Get user from session
        $userId = $this->request->getSession()->getUser()->getId();

        // Get all booked flights
        $flights = $this->passengerRepository->getBookedFlights($userId);

        return $this->render("Flight/booked-flights.html.twig", [
            'flights' => $flights
        ]);
    }
}