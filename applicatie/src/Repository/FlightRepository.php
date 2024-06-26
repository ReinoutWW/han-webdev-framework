<?php

namespace App\Repository;

use App\Entity\Flight;
use App\Repository\Filters\SearchFilters;
use DateTimeImmutable;
use Doctrine\DBAL\Connection;
use RWFramework\Framework\Http\NotFoundException;

class FlightRepository {
    public function __construct(
        private Connection $connection,
        private FlightMapper $flightMapper
    ) { }

    public function findByFlightNumber(int $flightNumber): Flight {
        $queryBuilder = $this->connection->createQueryBuilder();
    
        $queryBuilder->select(
            'v.vluchtnummer',
            'v.bestemming',
            'v.gatecode',
            'v.max_aantal',
            'v.max_gewicht_pp',
            'v.max_totaalgewicht',
            'v.vertrektijd',
            'v.maatschappijcode',
            'm.naam AS airline',
            'COUNT(p.passagiernummer) AS seatsTaken'
        )
        ->from('Vlucht', 'v')
        ->leftJoin('v', 'Passagier', 'p', 'p.vluchtnummer = v.vluchtnummer')
        ->leftJoin('v', 'Maatschappij', 'm', 'm.maatschappijcode = v.maatschappijcode')
        ->where('v.vluchtnummer = :vluchtnummer')
        ->setParameter('vluchtnummer', $flightNumber)
        ->groupBy('v.vluchtnummer', 'v.bestemming', 'v.gatecode', 'v.max_aantal', 'v.max_gewicht_pp', 'v.max_totaalgewicht', 'v.vertrektijd', 'v.maatschappijcode', 'm.naam');
    
        $result = $queryBuilder->executeQuery();
    
        $row = $result->fetchAssociative();
    
        if (!$row) {
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
            airlineCode: $row['maatschappijcode'],
            airline: $row['airline'],
            seatsTaken: $row['seatsTaken']
        );
    }
    
    public function flightExists (int $flightNumber) {
        $queryBuilder = $this->connection->createQueryBuilder();
    
        // Check if flightNumber is already in the database
        $queryBuilder->select('COUNT(*) as aantal')
            ->from('Vlucht')
            ->where('vluchtnummer = :vluchtnummer')
            ->setParameter('vluchtnummer', $flightNumber);
            
        $result = $queryBuilder->executeQuery();
            
        $row = $result->fetchAssociative();

        // Return if has found a flight with the given flightNumber
        return $row['aantal'] === "1";
    }

    public function findFlightsByFlightNumbers(array $flightNumbers) {
        $queryBuilder = $this->connection->createQueryBuilder();
    
        $queryBuilder->select(
            'v.vluchtnummer',
            'v.bestemming',
            'v.gatecode',
            'v.max_aantal',
            'v.max_gewicht_pp',
            'v.max_totaalgewicht',
            'v.vertrektijd',
            'v.maatschappijcode',
            'm.naam AS airline',
            'COUNT(p.passagiernummer) AS seatsTaken'
        )
        ->from('Vlucht', 'v')
        ->leftJoin('v', 'Passagier', 'p', 'p.vluchtnummer = v.vluchtnummer')
        ->leftJoin('v', 'Maatschappij', 'm', 'm.maatschappijcode = v.maatschappijcode')
        ->where('v.vluchtnummer IN (:vluchtnummers)')
        ->setParameter('vluchtnummers', $flightNumbers, Connection::PARAM_INT_ARRAY)
        ->groupBy('v.vluchtnummer', 'v.bestemming', 'v.gatecode', 'v.max_aantal', 'v.max_gewicht_pp', 'v.max_totaalgewicht', 'v.vertrektijd', 'v.maatschappijcode', 'm.naam');
    
        $result = $queryBuilder->executeQuery();
    
        $flights = [];
    
        while ($row = $result->fetchAssociative()) {
            $flights[] = Flight::create(
                flightNumber: $row['vluchtnummer'],
                destination: $row['bestemming'],
                gate: $row['gatecode'],
                maxPassengers: $row['max_aantal'],
                maxWeightPerPassenger: $row['max_gewicht_pp'],
                maxTotalWeight: $row['max_totaalgewicht'],
                departureTime: new \DateTimeImmutable($row['vertrektijd']),
                airlineCode: $row['maatschappijcode'],
                airline: $row['airline'],
                seatsTaken: $row['seatsTaken']
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

    /**
     * Will return a list of flights based on the given filters
     * Note: If no filters are set, only future flights will be shown
     */
    public function getFlights(int $page = 1, SearchFilters $filters = null, bool $onlyFutureFlights = false, int $limit = 10): array {
        $queryBuilder = $this->connection->createQueryBuilder();
    
        $queryBuilder->select(
            'v.vluchtnummer',
            'v.bestemming',
            'v.gatecode',
            'v.max_aantal',
            'v.max_gewicht_pp',
            'v.max_totaalgewicht',
            'v.vertrektijd',
            'v.maatschappijcode',
            'm.naam AS airline',
            'COUNT(p.passagiernummer) AS seatsTaken'
        )
        ->from('Vlucht', 'v')
        ->join('v', 'Luchthaven', 'l', 'l.luchthavencode = v.bestemming')
        ->leftJoin('v', 'Passagier', 'p', 'p.vluchtnummer = v.vluchtnummer')
        ->leftJoin('v', 'Maatschappij', 'm', 'm.maatschappijcode = v.maatschappijcode')
        ->setFirstResult(($page - 1) * $limit)
        ->setMaxResults($limit)
        ->groupBy('v.vluchtnummer', 'v.bestemming', 'v.gatecode', 'v.max_aantal', 'v.max_gewicht_pp', 'v.max_totaalgewicht', 'v.vertrektijd', 'v.maatschappijcode', 'm.naam');
    
        // If no filters are set, only show future flights
        if ($onlyFutureFlights) {
            $queryBuilder->andWhere('v.vertrektijd > GETDATE()');
        }
    
        // Add filters to the query
        if ($filters !== null) {
            foreach ($filters->getFilters() as $key => $value) {
                if ($value instanceof \DateTimeImmutable) {
                    $queryBuilder->andWhere("v.$key = :$key")
                        ->setParameter($key, $value->format('Y-m-d H:i:s'));
                } else {
                    $queryBuilder->andWhere("v.$key = :$key")
                        ->setParameter($key, $value);
                }
            }
        }
    
        $result = $queryBuilder->executeQuery();
    
        $flights = [];
    
        while ($row = $result->fetchAssociative()) {
            $flights[] = Flight::create(
                flightNumber: $row['vluchtnummer'],
                destination: $row['bestemming'],
                gate: $row['gatecode'],
                maxPassengers: $row['max_aantal'],
                maxWeightPerPassenger: $row['max_gewicht_pp'],
                maxTotalWeight: $row['max_totaalgewicht'],
                departureTime: new \DateTimeImmutable($row['vertrektijd']),
                airlineCode: $row['maatschappijcode'],
                airline: $row['airline'],
                seatsTaken: $row['seatsTaken']
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

    public function getMaxLuggagePerPassenger($flightNumber) {
        $queryBuilder = $this->connection->createQueryBuilder();

        $queryBuilder->select('m.max_objecten_pp')
            ->from('Vlucht', 'v')
            ->join('v', 'Maatschappij', 'm', 'm.maatschappijcode = v.maatschappijcode')
            ->where('v.vluchtnummer = :vluchtnummer')
            ->setParameter('vluchtnummer', $flightNumber);

        $result = $queryBuilder->executeQuery();

        $row = $result->fetchAssociative();

        return $row['max_objecten_pp'] ?? 0;
    }

    public function save(Flight $flight): void {
        $this->flightMapper->save($flight);
    }
}