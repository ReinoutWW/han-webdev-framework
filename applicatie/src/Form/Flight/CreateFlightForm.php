<?php

namespace App\Form\Flight;

use App\Entity\Flight;
use App\Form\AbstractForm;
use App\Repository\AirportRepository;
use App\Repository\FlightMapper;
use App\Repository\FlightRepository;

class CreateFlightForm extends AbstractForm
{
    private Flight $flight;

    public function __construct(
        private FlightMapper $flightMapper,
        private FlightRepository $flightRepository
    ) {}

    public function save(): void {
        $this->flightMapper->save($this->flight);
    }

    public function setFlight(Flight $flight): void {
        $this->flight = $flight;
    }

    public function validate(): void {
        // Check if the flight number is unique
        $planeExists = $this->flightRepository->flightExists($this->flight->getFlightNumber());

        if($planeExists) {
            $this->addError('Het ingevoerde vluchtnummer bestaat al.');
        }

        // Check if the flight number is between 1000 and 9999
        if($this->flight->getFlightNumber() !== null) {
            if($this->flight->getFlightNumber() < 10000 || $this->flight->getFlightNumber() > 99999) {
                dd($this->flight->getFlightNumber());
                $this->addError('Het vluchtnummer moet tussen 1000 en 9999 liggen.');
            }
        } else {
            $this->addError('Het vluchtnummer is verplicht.');
        }

        // Check if weight is between 0 and 200
        if($this->flight->getMaxWeightPerPassenger() < 0 || $this->flight->getMaxWeightPerPassenger() > 200) {
            $this->addError('Het maximaal gewicht per passagier moet tussen 0 en 200 liggen.');
        }

        // Check if the departure time is in the future
        if($this->flight->getDepartureTime() < new \DateTimeImmutable()) {
            $this->addError('De vertrektijd moet in de toekomst liggen.');
        }

        // Check max total weight is between 0 and 10000
        if($this->flight->getMaxTotalWeight() < 0 || $this->flight->getMaxTotalWeight() > 10000) {
            $this->addError('Het maximaal totaalgewicht moet tussen 0 en 10000 liggen.');
        }
    }    
}