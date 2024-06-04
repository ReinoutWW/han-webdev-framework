<?php

namespace App\Repository;

use App\Entity\Luggage;
use RWFramework\Framework\Dbal\DataMapper;

/**
 * Data mapper pattern
 */
class LuggageMapper {
    public function __construct(
        private DataMapper $dataMapper
    ) {}

    public function save(Luggage $luggage): void {
        $stmt = $this->dataMapper->getConnection()->prepare("
            INSERT INTO BagageObject (passagiernummer, objectvolgnummer, gewicht, vluchtnummer)
            VALUES (:passenger_number, :object_follow_number, :weight, :flight_number)
        ");

        $stmt->bindValue(':passenger_number', $luggage->getPassengerNumber());
        $stmt->bindValue(':object_follow_number', $luggage->getObjectFollowNumber());
        $stmt->bindValue(':weight', $luggage->getWeight());
        $stmt->bindValue(':flight_number', $luggage->getFlightNumber());

        $stmt->executeStatement();
    }
}