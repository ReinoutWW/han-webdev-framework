<?php

namespace App\Repository;

use App\Entity\Passenger;
use App\Entity\User;

class PassengerRepository
{
    public function __construct(
        private PassengerMapper $passengerMapper
    ) { }

    public function addToFlight(User $user, int $flightNumber, string $seatNumber): void
    {
        $passenger = Passenger::create(
            userId: $user->getId(),
            name: $user->getName(),
            gender: $user->getGender(),
            flightNumber: $flightNumber,
            seatNumber: $seatNumber
        );

        $this->passengerMapper->save($passenger);
    }
}