<?php

namespace App\Repository;

use App\Entity\Flight;
use Doctrine\DBAL\Connection;
use RWFramework\Framework\Http\NotFoundException;

class FlightRepository {
    public function __construct(
        private Connection $connection,
        private FlightMapper $flightMapper
    ) { }

    public function findByFlightNumber(int $flightNumber) {
        $queryBuilder = $this->connection->createQueryBuilder();

        $queryBuilder->select('vluchtnummer', 'bestemming', 'gatecode', 'max_aantal', 'max_gewicht_pp', 'max_totaalgewicht', 'vertrektijd', 'maatschappijcode')
            ->from('Vlucht')
            ->where('vluchtnummer = :vluchtnummer')
            ->setParameter('vluchtnummer', $flightNumber);

        $result = $queryBuilder->executeQuery();

        $row = $result->fetchAssociative();

        if(!$row) {
            throw new NotFoundException("Vlucht met vluchtnummer $flightNumber is niet gevonden.");
        }

        return Flight::create(
            flightNumber: $row['vluchtnummer'],
            destination: $row['bestemming'],
            gate: $row['gatecode'],
            maxPassengers: $row['max_aantal'],
            maxWeightPerPassenger: $row['max_gewicht_pp'],
            maxTotalWeight: $row['max_totaalgewicht'],
            departureTime: new \DateTimeImmutable($row['vertrektijd']),
            airlineCode: $row['maatschappijcode']
        );
    }

    public function findFlightsByFlightNumbers(array $flightNumbers) {
        $queryBuilder = $this->connection->createQueryBuilder();

        $queryBuilder->select('vluchtnummer', 'bestemming', 'gatecode', 'max_aantal', 'max_gewicht_pp', 'max_totaalgewicht', 'vertrektijd', 'maatschappijcode')
            ->from('Vlucht')
            ->where('vluchtnummer IN (:vluchtnummers)')
            ->setParameter('vluchtnummers', $flightNumbers, Connection::PARAM_INT_ARRAY);

        $result = $queryBuilder->executeQuery();

        $flights = [];

        while($row = $result->fetchAssociative()) {
            $flights[] = Flight::create(
                flightNumber: $row['vluchtnummer'],
                destination: $row['bestemming'],
                gate: $row['gatecode'],
                maxPassengers: $row['max_aantal'],
                maxWeightPerPassenger: $row['max_gewicht_pp'],
                maxTotalWeight: $row['max_totaalgewicht'],
                departureTime: new \DateTimeImmutable($row['vertrektijd']),
                airlineCode: $row['maatschappijcode']
            );
        }

        return $flights;
    }

    public function getMaxSeatsByFlightNumber(int $flightNumber) {
        $queryBuilder = $this->connection->createQueryBuilder();

        $queryBuilder->select('max_aantal')
            ->from('Vlucht')
            ->where('vluchtnummer = :vluchtnummer')
            ->setParameter('vluchtnummer', $flightNumber);

        $result = $queryBuilder->executeQuery();

        $row = $result->fetchAssociative();

        if(!$row) {
            throw new NotFoundException("Vlucht met vluchtnummer $flightNumber is niet gevonden.");
        }

        return $row['max_aantal'];
    }

    public function getSeatsOcupiedByFlightNumber (int $flightNumber): int {
        $queryBuilder = $this->connection->createQueryBuilder();

        $queryBuilder->select('COUNT(*) as seats')
            ->from('Passagier')
            ->where('vluchtnummer = :vluchtnummer')
            ->setParameter('vluchtnummer', $flightNumber);

        $result = $queryBuilder->executeQuery();

        $row = $result->fetchAssociative();

        return $row['seats'];
    }

    public function getSeatsAvailableByFlightNumber(int $flightNumber) {
        $seatsAvailable = $this->getMaxSeatsByFlightNumber($flightNumber);
        $seatsOcupied = $this->getSeatsOcupiedByFlightNumber($flightNumber);

        return $seatsAvailable - $seatsOcupied;
    }

    public function areSeatsFullByFlightNumber(int $flightNumber): bool {
        return $this->getSeatsAvailableByFlightNumber($flightNumber) === 0;
    }

    public function getNextAvailableSeatByFlightNumber(int $flightNumber): ?string {
        if($this->areSeatsFullByFlightNumber($flightNumber)) {
            return null;
        }

        $queryBuilder = $this->connection->createQueryBuilder();

        $queryBuilder->select('COUNT(*) as aantalBezet')
            ->from('Passagier')
            ->where('vluchtnummer = :vluchtnummer')
            ->setParameter('vluchtnummer', $flightNumber);

        $result = $queryBuilder->executeQuery();

        $row = $result->fetchAssociative();

        if(!$row) {
            return null;
        }

        $seat = 'A'. ($row['aantalBezet'] + 1);

        return $seat;
    }

    public function getFlights(int $page = 1) {
        $queryBuilder = $this->connection->createQueryBuilder();

        $maxResults = 10;

        $queryBuilder->select('vluchtnummer', 'bestemming', 'gatecode', 'max_aantal', 'max_gewicht_pp', 'max_totaalgewicht', 'vertrektijd', 'maatschappijcode')
            ->from('Vlucht')
            ->setFirstResult(($page - 1) * $maxResults)
            ->setMaxResults($maxResults);

        $result = $queryBuilder->executeQuery();

        $flights = [];

        while($row = $result->fetchAssociative()) {
            $flights[] = Flight::create(
                flightNumber: $row['vluchtnummer'],
                destination: $row['bestemming'],
                gate: $row['gatecode'],
                maxPassengers: $row['max_aantal'],
                maxWeightPerPassenger: $row['max_gewicht_pp'],
                maxTotalWeight: $row['max_totaalgewicht'],
                departureTime: new \DateTimeImmutable($row['vertrektijd']),
                airlineCode: $row['maatschappijcode']
            );
        }

        return $flights;
    }

    public function isSeatAvailable($flightNumber, $seatNumber) {
        $queryBuilder = $this->connection->createQueryBuilder();

        $queryBuilder->select('COUNT(*) as aantal')
            ->from('Passagier')
            ->where('vluchtnummer = :vluchtnummer AND stoel = :stoel')
            ->setParameters([
                'vluchtnummer' => $flightNumber,
                'stoel' => $seatNumber
            ]);

        $result = $queryBuilder->executeQuery();

        $row = $result->fetchAssociative();

        return $row['aantal'] === "0";
    }

    public function getMaxPersonalLuggageWeight(int $flightNumber) {
        $queryBuilder = $this->connection->createQueryBuilder();

        $queryBuilder->select('max_gewicht_pp')
            ->from('Vlucht')
            ->where('vluchtnummer = :vluchtnummer')
            ->setParameter('vluchtnummer', $flightNumber);

        $result = $queryBuilder->executeQuery();

        $row = $result->fetchAssociative();

        return $row['max_gewicht_pp'] ?? 0;
    }

    public function getMaxFlightLuggageWeight(int $flightNumber) {
        $queryBuilder = $this->connection->createQueryBuilder();

        $queryBuilder->select('max_totaalgewicht')
            ->from('Vlucht')
            ->where('vluchtnummer = :vluchtnummer')
            ->setParameter('vluchtnummer', $flightNumber);

        $result = $queryBuilder->executeQuery();

        $row = $result->fetchAssociative();

        return $row['max_totaalgewicht'] ?? 0;
    }

    public function getTotalPassengerLuggageWeight($flightNumber) {
        $queryBuilder = $this->connection->createQueryBuilder();

        $queryBuilder->select('SUM(gewicht) as totaal_gewicht')
            ->from('BagageObject')
            ->where('vluchtnummer = :vluchtnummer')
            ->setParameter('vluchtnummer', $flightNumber);

        $result = $queryBuilder->executeQuery();

        $row = $result->fetchAssociative();

        return $row['totaal_gewicht'] ?? 0;
    }

    public function save(Flight $flight): void {
        $this->flightMapper->save($flight);
    }
}