<?php

namespace App\Entity;

class Airline {
    public function __construct(
        private string $airlineCode,
        private string $name,
        private int $maxObjectsPerPessenger
    ) { }

    public static function create(string $airlineCode, string $name, int $maxObjectsPerPessenger): self
    {
        return new self($airlineCode, $name, $maxObjectsPerPessenger);
    }

    public function getAirlineCode(): string {
        return $this->airlineCode;
    }

    public function getName(): string {
        return $this->name;
    }

    public function getMaxObjectsPerPessenger(): int
    {
        return $this->maxObjectsPerPessenger;
    }
}