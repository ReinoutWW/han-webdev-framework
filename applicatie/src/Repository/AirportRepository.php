<?php

namespace App\Repository;

use App\Entity\Airport;
use Doctrine\DBAL\Connection;

class AirportRepository {
    public function __construct(
        private Connection $connection,
    ) { }

    public function getAirports() {
        $queryBuilder = $this->connection->createQueryBuilder();

        $queryBuilder->select('luchthavencode', 'naam', 'land')
            ->from('Luchthaven');

        $result = $queryBuilder->executeQuery();

        $airports = [];

        while ($row = $result->fetchAssociative()) {
            $airports[] = Airport::create(
                $row['luchthavencode'],
                $row['naam'],
                $row['land'],
            );
        }
        

        return $airports;
    }

    public function getGates() {
        $queryBuilder = $this->connection->createQueryBuilder();

        $queryBuilder->select('gatecode')
        ->from('Gate');

        $result = $queryBuilder->executeQuery();

        $gates = [];
        while ($row = $result->fetchAssociative()) {
            $gates[] = $row['gatecode'];
        }

        return $gates;
    }
}