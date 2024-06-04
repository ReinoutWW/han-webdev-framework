<?php

namespace App\Repository;

use App\Entity\Flight;
use RWFramework\Framework\Dbal\DataMapper;

/**
 * Data mapper pattern
 */
class FlightMapper {
    public function __construct(
        private DataMapper $dataMapper
    ) {}

    public function save(Flight $flight): void {
        $stmt = $this->dataMapper->getConnection()->prepare("
            INSERT INTO Vlucht (vluchtnummer, bestemming, gatecode, max_aantal, max_gewicht_pp, max_totaalgewicht, vertrektijd, maatschappijcode)
            VALUES (:vluchtnummer, :bestemming, :gatecode, :max_aantal, :max_gewicht_pp, :max_totaalgewicht, :vertrektijd, :maatschappijcode)
        ");
    
        // Ensure values fit within expected ranges
        $maxAantal = (int)$flight->getMaxPassengers();
        $maxGewichtPP = (float)$flight->getMaxWeightPerPassenger();
        $maxTotaalGewicht = (float)$flight->getMaxTotalWeight();
    
        $stmt->bindValue(':vluchtnummer', (int)$flight->getFlightNumber());
        $stmt->bindValue(':bestemming', $flight->getDestination());
        $stmt->bindValue(':gatecode', $flight->getGate());
        $stmt->bindValue(':max_aantal', $maxAantal);
        $stmt->bindValue(':max_gewicht_pp', $maxGewichtPP);
        $stmt->bindValue(':max_totaalgewicht', $maxTotaalGewicht);
        $stmt->bindValue(':vertrektijd', $flight->getDepartureTime()->format("Y-m-d H:i:s"));
        $stmt->bindValue(':maatschappijcode', $flight->getAirlineCode());
    
        $stmt->executeStatement();
    
        $this->dataMapper->save($flight);
    }
}