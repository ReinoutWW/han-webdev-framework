<?php

namespace App\Repository;

use App\Entity\Luggage;
use Doctrine\DBAL\Connection;

class LuggageRepository {
    public function __construct(
        private Connection $connection,
        private LuggageMapper $luggageMapper
    ) { }

    public function save(Luggage $luggage) {
        $this->luggageMapper->save($luggage);
    }

    public function getLuggage(int $flightNumber, int $passengerNumber): array {
        // Do the logic here
        $queryBuilder = $this->connection->createQueryBuilder();

        $queryBuilder
            ->select('gewicht', 'objectvolgnummer')
            ->from('BagageObject')
            ->where('vluchtnummer = :vluchtnummer AND passagiernummer = :passagiernummer')
        ->setParameters([
            'vluchtnummer' => $flightNumber,
            'passagiernummer' => $passengerNumber
        ]);

        $result = $queryBuilder->executeQuery();

        $rows = $result->fetchAllAssociative();

        $luggage = [];

        foreach ($rows as $row) {
            $luggage[] = Luggage::create(
                passengerNumber: $passengerNumber,
                objectFollowNumber: $row['objectvolgnummer'],
                weight: $row['gewicht'],
                flightNumber: $flightNumber
            );
        }

        return $luggage;
    }

    public function getNextObjectFollowNumber(int $flightNumber, int $passengerNumber): int {
        $queryBuilder = $this->connection->createQueryBuilder();

        $queryBuilder
            ->select('MAX(objectvolgnummer) as max_objectvolgnummer')
            ->from('BagageObject')
            ->where('vluchtnummer = :vluchtnummer AND passagiernummer = :passagiernummer')
        ->setParameters([
            'vluchtnummer' => $flightNumber,
            'passagiernummer' => $passengerNumber
        ]);

        $result = $queryBuilder->executeQuery();

        $row = $result->fetchAssociative();

        return $row['max_objectvolgnummer'] + 1 ?? 0;
    }

    public function deleteLuggage(int $flightNumber, int $passengerNumber, int $objectFollowNumber) {
        $queryBuilder = $this->connection->createQueryBuilder();

        $queryBuilder
            ->delete('BagageObject')
            ->where('vluchtnummer = :vluchtnummer AND passagiernummer = :passagiernummer AND objectvolgnummer = :objectvolgnummer')
        ->setParameters([
            'vluchtnummer' => $flightNumber,
            'passagiernummer' => $passengerNumber,
            'objectvolgnummer' => $objectFollowNumber
        ]);

        $queryBuilder->executeStatement();
    }

    public function getTotalPassengerLuggageWeight(int $flightNumber, int $passengerNumber): int {
        $queryBuilder = $this->connection->createQueryBuilder();

        $queryBuilder
            ->select('SUM(gewicht) as totaal_gewicht')
            ->from('BagageObject')
            ->where('vluchtnummer = :vluchtnummer AND passagiernummer = :passagiernummer')
        ->setParameters([
            'vluchtnummer' => $flightNumber,
            'passagiernummer' => $passengerNumber
        ]);

        $result = $queryBuilder->executeQuery();

        $row = $result->fetchAssociative();

        return $row['totaal_gewicht'] ?? 0;
    }
}