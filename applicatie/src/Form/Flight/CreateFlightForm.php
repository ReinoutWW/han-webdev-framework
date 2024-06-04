<?php

namespace App\Form\Flight;

use App\Entity\Flight;
use App\Form\AbstractForm;
use App\Repository\FlightMapper;

class CreateFlightForm extends AbstractForm
{
    private Flight $flight;

    public function __construct(
        private FlightMapper $flightMapper
    ) {}

    public function save(): void {
        $this->flightMapper->save($this->flight);
    }

    public function setFlight(Flight $flight): void {
        $this->flight = $flight;
    }

    public function validate(): void {
        // Check if the flight number is unique

        // Check if the flight number is between 1000 and 9999

        // Check if weight is between 0 and 200

        // Check if the departure time is in the future

        // Check if the airline code is 2 characters long

        // Check if the airline code exists in the database

        // Validate if the Gatecode exists in the database

    }    
}