<?php

namespace App\Repository;

use App\Entity\Luggage;
use App\Entity\Passenger;
use App\Entity\User;
use Doctrine\DBAL\Connection;

class PassengerRepository
{
    public function __construct(
        private PassengerMapper $passengerMapper,
        private FlightRepository $flightRepository,
        private Connection $connection
    ) { }

    public function addToFlight(User $user, int $flightNumber, string $seatNumber): void
    {
        $passenger = Passenger::create(
            passengerNumber: null,
            userId: $user->getId(),
            name: $user->getName(),
            gender: $user->getGender(),
            flightNumber: $flightNumber,
            seatNumber: $seatNumber
        );

        $this->passengerMapper->save($passenger);
    }

    public function getPassengersByFlightNumber(int $flightNumber): array
    {
        // Do the logic here
        $queryBuilder = $this->connection->createQueryBuilder();

        $queryBuilder
            ->select('*')
            ->from('Passagier')
            ->where('vluchtnummer = :vluchtnummer')
        ->setParameter('vluchtnummer', $flightNumber);

        $result = $queryBuilder->executeQuery();

        $rows = $result->fetchAllAssociative();

        $passengers = [];

        foreach ($rows as $row) {
            $passenger = Passenger::create(
                passengerNumber: $row['passagiernummer'],
                userId: $row['userId'],
                name: $row['naam'],
                gender: $row['geslacht'],
                flightNumber: $row['vluchtnummer'],
                seatNumber: $row['stoel']
            );

            $passengers[] = $passenger;
        }

        return $passengers;
    }

    public function getBookedFlights(int $userId): array
    {
        // Do the logic here
        $queryBuilder = $this->connection->createQueryBuilder();

        $queryBuilder
            ->select('vluchtnummer')
            ->from('Passagier')
            ->where('userId = :userId')
        ->setParameter('userId', $userId);

        $result = $queryBuilder->executeQuery();

        $rows = $result->fetchAllAssociative();

        $flights = $this->flightRepository->findFlightsByFlightNumbers(
            array_map(fn($row) => $row['vluchtnummer'], $rows)
        );

        return $flights;
    }

    public function getBookedFlightPassengerDetails(int $userId, int $flightNumber): ?Passenger
    {
        // Do the logic here
        $queryBuilder = $this->connection->createQueryBuilder();

        $queryBuilder
            ->select('*')
            ->from('Passagier')
            ->where('userId = :userId AND vluchtnummer = :vluchtnummer')
        ->setParameters([
            'userId' => $userId,
            'vluchtnummer' => $flightNumber
        ]);

        $result = $queryBuilder->executeQuery();

        $row = $result->fetchAssociative();

        if (!$row) {
            return null;
        }

        // Create new Passenger
        $passenger = Passenger::create(
            passengerNumber: $row['passagiernummer'],
            userId: $row['userId'],
            name: $row['naam'],
            gender: $row['geslacht'],
            flightNumber: $row['vluchtnummer'],
            seatNumber: $row['stoel']
        );

        return $passenger;
    }

    public function hasSeat(int $flightNumber, int $userId): bool {
        // Do the logic here
        $queryBuilder = $this->connection->createQueryBuilder();

        $queryBuilder
            ->select('COUNT(*) as aantal')
            ->from('Passagier')
            ->where('vluchtnummer = :vluchtnummer AND userId = :userId')
        ->setParameters([
            'vluchtnummer' => $flightNumber,
            'userId' => $userId
        ]);

        $result = $queryBuilder->executeQuery();

        $row = $result->fetchAssociative();

        return $row['aantal'] > 0;
    }

    public function getPassengerNumber(int $flightNumber, int $userId): ?int {
        // Do the logic here
        $queryBuilder = $this->connection->createQueryBuilder();

        $queryBuilder
            ->select('passagiernummer')
            ->from('Passagier')
            ->where('vluchtnummer = :vluchtnummer AND userId = :userId')
        ->setParameters([
            'vluchtnummer' => $flightNumber,
            'userId' => $userId
        ]);

        $result = $queryBuilder->executeQuery();

        $row = $result->fetchAssociative();

        return $row['passagiernummer'] ?? null;
    }
}