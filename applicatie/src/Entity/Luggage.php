<?php

namespace App\Entity;
use RWFramework\Framework\Dbal\Entity;

class Luggage extends Entity {
    public function __construct(
        private int $passengerNumber,
        private string $objectFollowNumber,
        private string $weight,
        private string $flightNumber
    ) {}

    public static function create(
        int $passengerNumber,
        string $objectFollowNumber,
        string $weight,
        string $flightNumber
    ): self {
        return new self(
            passengerNumber: $passengerNumber,
            objectFollowNumber: $objectFollowNumber,
            weight: $weight,
            flightNumber: $flightNumber
        );
    }

    public function getPassengerNumber(): int {
        return $this->passengerNumber;
    }

    public function getObjectFollowNumber(): string {
        return $this->objectFollowNumber;
    }

    public function getWeight(): string {
        return $this->weight;
    }

    public function getFlightNumber(): string {
        return $this->flightNumber;
    }
}