<?php

namespace App\Repository;

use App\Entity\Passenger;
use RWFramework\Framework\Dbal\DataMapper;

class PassengerMapper {
    
    public function __construct(
        private DataMapper $dataMapper
    ) {}

    public function save(Passenger $passenger): void {
        $stmt = $this->dataMapper->getConnection()->prepare("
            INSERT INTO Passagier (naam, userId, vluchtnummer, stoel)
            VALUES (:naam, :userId, :vluchtnummer, :stoel)
        ");

        $stmt->bindValue(':naam', $passenger->getName());
        $stmt->bindValue(':userId', $passenger->getUserId());
        $stmt->bindValue(':vluchtnummer', $passenger->getFlightNumber());
        $stmt->bindValue(':stoel', $passenger->getSeatNumber());
        
        $stmt->executeStatement();

        $id = $this->dataMapper->save($passenger);

        $passenger->setId($id);
    }
}