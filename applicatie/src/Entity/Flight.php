<?php

namespace App\Entity;

use RWFramework\Framework\Dbal\Entity;

class Flight extends Entity {
    public function __construct(
        private int $flightNumber,
        private string $destination,
        private string $gate,
        private float $maxPassengers,
        private float $maxWeightPerPassenger,
        private float $maxTotalWeight,
        private \DateTimeImmutable $departureTime,
        private string $airlineCode,
        private string $airline = '',  
        private int $seatsTaken = 0
    ) {
        
    }

    public static function create(
        int $flightNumber, 
        string $destination, 
        string $gate, 
        float $maxPassengers, 
        float $maxWeightPerPassenger, 
        float $maxTotalWeight, 
        \DateTimeImmutable $departureTime, 
        string $airlineCode,
        string $airline = '',
        int $seatsTaken = 0
    ): Flight {
        return new self(
            flightNumber: $flightNumber, 
            destination: $destination, 
            gate: $gate, 
            maxPassengers: $maxPassengers, 
            maxWeightPerPassenger: $maxWeightPerPassenger, 
            maxTotalWeight: $maxTotalWeight, 
            departureTime: $departureTime, 
            airlineCode: $airlineCode,
            airline: $airline,
            seatsTaken: $seatsTaken
        );
    }

    public function getFlightNumber(): int {
        return $this->flightNumber;
    }

    public function getDestination(): string {
        return $this->destination;
    }

    public function getGate(): string {
        return $this->gate;
    }

    public function getMaxPassengers(): int {
        return $this->maxPassengers;
    }

    public function getMaxWeightPerPassenger(): float {
        return $this->maxWeightPerPassenger;
    }

    public function getMaxTotalWeight(): float {
        return $this->maxTotalWeight;
    }

    public function getDepartureTime(): \DateTimeImmutable {
        return $this->departureTime;
    }

    public function getAirlineCode(): string {
        return $this->airlineCode;
    }

    public function getAirline(): string {
        return $this->airline;
    }

    public function getSeatsTaken(): int {
        return $this->seatsTaken;
    }

    public function getAvailableSeatsCount(): int {
        return $this->maxPassengers - $this->seatsTaken;
    }
}