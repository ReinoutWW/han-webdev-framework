<?php

namespace App\Controllers;

use App\Entity\Luggage;
use App\Form\Flight\LuggageForm;
use App\Repository\FlightRepository;
use App\Repository\LuggageMapper;
use App\Repository\LuggageRepository;
use App\Repository\PassengerRepository;
use App\Roles\RoleConstants;
use RWFramework\Framework\Controller\AbstractController;
use RWFramework\Framework\Http\RedirectResponse;
use RWFramework\Framework\Session\Session;
use RWFramework\Framework\Session\SessionInterface;

class PassengerController extends AbstractController {
    private const FLIGHTS_PER_PAGE = 10;

    public function __construct(
        private FlightRepository $flightRepository,
        private PassengerRepository $passengerRepository,
        private LuggageRepository $luggageRepository,
        private LuggageMapper $luggageMapper,
        private SessionInterface $session
    ){}

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

        // Check if the user already has a seat on the flight
        $hasSeat = $this->passengerRepository->hasSeat($flightNumber, $user->getId());

        if($hasSeat) {
            $this->request->getSession()->setFlash(Session::NOTIFICATION_ERROR, 'U heeft reeds een stoel geboekt op deze vlucht.');
            return new RedirectResponse('/vluchten/' . $flightNumber);
        }

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
            return new RedirectResponse($this->session->getLastVisitedRoute());
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
            'flights' => $flights,
            'max_results' => self::FLIGHTS_PER_PAGE
        ]);
    }

    public function luggage(int $flightNumber, int $passengerNumber) {
        $userId = $this->request->getSession()->getUser()->getId();
        $isAuthenticated = $this->isEmployeeOrPersonalDetails($passengerNumber, $userId);

        if(!$isAuthenticated) {
            $this->request->getSession()->setFlash(Session::NOTIFICATION_ERROR, 'U heeft geen toegang tot deze pagina.');
            return new RedirectResponse('/vluchten/' . $flightNumber);
        }

        // If the passengerNumber is not the session users' own passenger number, it's an employee viewing the page
        $employeeView = !$this->isPersonalPassengerNumber($passengerNumber, $userId);
        $passengerUsername = $employeeView ? $this->passengerRepository->getPassengerUsername($passengerNumber) : $this->request->getSession()->getUser()->getUsername();

        // Get the luggage registered by the user
        $luggageList = $this->luggageRepository->getLuggage($flightNumber, $passengerNumber);

        $currentPersonalLuggageWeight = $this->luggageRepository->getTotalPassengerLuggageWeight($flightNumber, $passengerNumber);
        $maxPersonalLuggageWeight = $this->flightRepository->getMaxPersonalLuggageWeight($flightNumber);
        $maxLuggagePerPassenger = $this->flightRepository->getMaxLuggagePerPassenger($flightNumber);

        $weightFree = ($maxPersonalLuggageWeight - $currentPersonalLuggageWeight) > 0 ? $maxPersonalLuggageWeight - $currentPersonalLuggageWeight : 0;

        return $this->render("Flight/luggage.html.twig", [
            'luggage' => $luggageList,
            'flightNumber' => $flightNumber,
            'passengerNumber' => $passengerNumber,
            'totalWeight' => $currentPersonalLuggageWeight,
            'maxLuggagePerPassenger' => $maxLuggagePerPassenger,
            'luggageWeightLeft' => $weightFree,
            'employeeView' => $employeeView,
            'passengerUsername' => $passengerUsername
        ]);
    }

    public function storeLuggage(int $flightNumber, int $passengerNumber) {
        // Check if the user has access to the flight
        $userId = $this->request->getSession()->getUser()->getId();
        $isAuthenticated = $this->isEmployeeOrPersonalDetails($passengerNumber, $userId);

        if(!$isAuthenticated) {
            $this->request->getSession()->setFlash(Session::NOTIFICATION_ERROR, 'U heeft geen toegang tot deze pagina.');
            return new RedirectResponse('/vluchten/' . $flightNumber);
        }

        $weight = $this->request->input('weight');

        $objectFollowNumber = $this->luggageRepository->getNextObjectFollowNumber($flightNumber, $passengerNumber);

        $luggage = Luggage::create(
            passengerNumber: $passengerNumber,
            flightNumber: $flightNumber,
            weight: $weight,
            objectFollowNumber: $objectFollowNumber
        );

        // Form server-side validation
        $form = new LuggageForm($this->luggageMapper);

        $maxPersonalLuggageWeight = $this->flightRepository->getMaxPersonalLuggageWeight($flightNumber);
        $maxFlightLuggageWeight = $this->flightRepository->getMaxFlightLuggageWeight($flightNumber);
        $currentTotalFlightLuggageWeight = $this->flightRepository->getTotalPassengerLuggageWeight($flightNumber);
        $currentPersonalLuggageWeight = $this->luggageRepository->getTotalPassengerLuggageWeight($flightNumber, $passengerNumber);
        $currentLuggageCount = $this->luggageRepository->getLuggageCount($flightNumber, $passengerNumber);
        $maxLuggagePerPassenger = $this->flightRepository->getMaxLuggagePerPassenger($flightNumber);

        $form->setLimits(
            $maxPersonalLuggageWeight,
            $maxFlightLuggageWeight,
            $currentTotalFlightLuggageWeight,
            $currentPersonalLuggageWeight,
            $currentLuggageCount,
            $maxLuggagePerPassenger
        );

        $form->setLuggage($luggage);
        
        // Validate the weight
        if($form->hasValidationErrors()) {
            foreach($form->getValidationErrors() as $error) {
                $this->request->getSession()->setFlash(Session::NOTIFICATION_ERROR, $error);
            }

            return new RedirectResponse($this->session->getLastVisitedRoute());
        }

        // Store
        $form->save();

        $this->request->getSession()->setFlash(Session::NOTIFICATION_SUCCESS, 'Uw bagage is succesvol geregistreerd!');

        return new RedirectResponse('/vluchten/' . $flightNumber . '/passagier/' . $passengerNumber . '/bagage');
    }

    public function deleteLuggage(int $flightNumber, int $passengerNumber, int $objectFollowNumber) {
        $userId = $this->request->getSession()->getUser()->getId();
        $isAuthenticated = $this->isEmployeeOrPersonalDetails($passengerNumber, $userId);

        if(!$isAuthenticated) {
            $this->request->getSession()->setFlash(Session::NOTIFICATION_ERROR, 'U heeft geen toegang tot deze pagina.');
            return new RedirectResponse('vluchten/' . $flightNumber);
        }

        $this->luggageRepository->deleteLuggage($flightNumber, $passengerNumber, $objectFollowNumber);

        $this->request->getSession()->setFlash(Session::NOTIFICATION_SUCCESS, 'Bagage is succesvol verwijderd!');

        return new RedirectResponse($this->session->getLastVisitedRoute());
        
    }

    private function isEmployeeOrPersonalDetails($passengerNumber, $userId) {
        $isAuthenticated = $this->isPersonalPassengerNumber($passengerNumber, $userId) || $this->request->getSession()->hasRole(RoleConstants::EMPLOYEE);

        return $isAuthenticated;
    }

    private function isPersonalPassengerNumber($passengerNumber, $userId) {
        $passengerUserId = $this->passengerRepository->getPassengerUserId($passengerNumber);
        $isOwnPassengerNumber = $userId === $passengerUserId;

        return $isOwnPassengerNumber;
    }

}