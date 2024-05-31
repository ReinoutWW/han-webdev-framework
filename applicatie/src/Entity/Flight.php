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
        private string $airlineCode
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
        string $airlineCode
    ): Flight {
        return new self(
            $flightNumber, 
            $destination, 
            $gate, 
            $maxPassengers, 
            $maxWeightPerPassenger, 
            $maxTotalWeight, 
            $departureTime, 
            $airlineCode);
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
}