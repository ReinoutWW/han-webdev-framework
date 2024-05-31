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

    public function save(Flight $flight): void {
        $this->flightMapper->save($flight);
    }
}