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
            INSERT INTO passengers (passenger_number, user_id, flight_number, counter_number, seat_number, check_in_time, created_at)
            VALUES (:passenger_number, :user_id, :flight_number, :counter_number, :seat_number, :check_in_time, :created_at)
        ");

        $stmt->bindValue(':passenger_number', $passenger->getPassengerNumber());
        $stmt->bindValue(':user_id', $passenger->getUserId());
        $stmt->bindValue(':flight_number', $passenger->getFlightNumber());
        $stmt->bindValue(':counter_number', $passenger->getCounterNumber());
        $stmt->bindValue(':seat_number', $passenger->getSeatNumber());
        $stmt->bindValue(':check_in_time', $passenger->getCheckInTime()->format('Y-m-d H:i:s'));
        $stmt->bindValue(':created_at', $passenger->getCreatedAt()->format('Y-m-d H:i:s'));
        
        $stmt->executeStatement();

        $id = $this->dataMapper->save($passenger);

        $passenger->setId($id);
    }
}