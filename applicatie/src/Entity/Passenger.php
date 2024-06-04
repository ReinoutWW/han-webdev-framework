<?php

namespace App\Entity;

use RWFramework\Framework\Dbal\Entity;

class Passenger extends Entity {
    public function __construct(
        private ?int $passengerNumber,
        private string $userId,
        private string $name,
        private string $gender,
        private string $flightNumber,
        private ?string $counterNumber,
        private string $seatNumber,
        private ?\DateTimeImmutable $checkInTime,
        private \DateTimeImmutable $createdAt
    ) {}

    public static function create(
        ?string $passengerNumber,
        string $userId,
        string $name,
        string $gender,
        string $flightNumber,
        string $seatNumber
    ): Passenger {
        return new self(
            passengerNumber: $passengerNumber,
            userId: $userId,
            name: $name,
            gender: $gender,
            flightNumber: $flightNumber,
            counterNumber: null,
            seatNumber: $seatNumber,
            checkInTime: null,
            createdAt: new \DateTimeImmutable()
        );
    }

    public function getPassengerNumber(): ?int {
        return $this->passengerNumber;
    }

    public function getUserId(): string {
        return $this->userId;
    }

    public function getFlightNumber(): string {
        return $this->flightNumber;
    }

    public function getCounterNumber(): ?string {
        return $this->counterNumber;
    }

    public function setCounterNumber(string $counterNumber): void {
        $this->counterNumber = $counterNumber;
    }

    public function getSeatNumber(): string {
        return $this->seatNumber;
    }

    public function setSeatNumber(string $seatNumber): void {
        $this->seatNumber = $seatNumber;
    }

    public function getCheckInTime(): ?\DateTimeImmutable {
        return $this->checkInTime;
    }

    public function setCheckInTime(\DateTimeImmutable $checkInTime): void {
        $this->checkInTime = $checkInTime;
    }

    public function getCreatedAt(): \DateTimeImmutable {
        return $this->createdAt;
    }

    public function setId(int $id): void {
        $this->passengerNumber = $id;
    }

    public function getName(): string {
        return $this->name;
    }

    public function getGender(): string {
        return $this->gender;
    }
}