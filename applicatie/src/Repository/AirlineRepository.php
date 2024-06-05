<?php

namespace App\Repository;

use App\Entity\Airline;
use Doctrine\DBAL\Connection;

class AirlineRepository {
    public function __construct(
        private Connection $connection,
    ) { }

    public function getAirlines() {
        $queryBuilder = $this->connection->createQueryBuilder();

        $queryBuilder->select('maatschappijcode', 'naam', 'max_objecten_pp')
            ->from('Maatschappij');

        $result = $queryBuilder->executeQuery();

        $airlines = [];

        while ($row = $result->fetchAssociative()) {
            $airlines[] = Airline::create(
                $row['maatschappijcode'],
                $row['naam'],
                $row['max_objecten_pp'],
            );
        }

        return $airlines;
    }
}