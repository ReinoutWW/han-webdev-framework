<?php

namespace App\Entity;

use RWFramework\Framework\Dbal\Entity;

class Airport extends Entity {
    public function __construct(
        private string $airportCode,
        private string $name,
        private string $country,
    ) { }

    public static function create(string $airportCode, string $name, string $country): self
    {
        return new self($airportCode, $name, $country);
    }

    public function getAirportCode(): string {
        return $this->airportCode;
    }

    public function getName(): string {
        return $this->name;
    }

    public function getCountry(): string {
        return $this->country;
    }
}