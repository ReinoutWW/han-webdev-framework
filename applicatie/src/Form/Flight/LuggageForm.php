<?php

namespace App\Form\Flight;

use App\Entity\Luggage;
use App\Form\AbstractForm;
use App\Repository\LuggageMapper;

class LuggageForm extends AbstractForm
{
    private const MAX_BAGGAGE_COUNT = 3;
    private Luggage $luggage;
    private int $maxPersonalLuggageWeight;
    private int $maxFlightLuggageWeight;
    private int $currentFlightLuggageWeight;
    private int $currentPersonalLuggageWeight;
    private int $currentAmountOfLuggageCount;

    public function __construct(
        private LuggageMapper $luggageMapper,
    ) {}

    public function save(): void {
        $this->luggageMapper->save($this->luggage);
    }

    public function setLuggage(Luggage $luggage): void {
        $this->luggage = $luggage;
    }

    public function setLimits(
            int $maxPersonalLuggageWeight, 
            int $maxFlightLuggageWeight, 
            int $currentFlightLuggageWeight, 
            int $currentPersonalLuggageWeight,
            int $currentAmountOfLuggageCount
    ): void {
        $this->maxPersonalLuggageWeight = $maxPersonalLuggageWeight;
        $this->maxFlightLuggageWeight = $maxFlightLuggageWeight;
        $this->currentFlightLuggageWeight = $currentFlightLuggageWeight;
        $this->currentPersonalLuggageWeight = $currentPersonalLuggageWeight;
        $this->currentAmountOfLuggageCount = $currentAmountOfLuggageCount;
    }

    public function validate(): void {
        if($this->currentAmountOfLuggageCount > self::MAX_BAGGAGE_COUNT) {
            $this->addError('Het maximale aantal bagage is ' . self::MAX_BAGGAGE_COUNT . ' stuks per persoon.');
        }

        // Validate if the luggage weight is not empty
        if(empty($this->luggage->getWeight())) {
            $this->addError('Gewicht is verplicht');
        }

        // Validate if the luggage weight is a number and is greater than 0
        if(!is_numeric($this->luggage->getWeight()) || $this->luggage->getWeight() <= 0) {
            $this->addError('Gewicht moet een getal groter dan 0 zijn');
        }

        // Validate if the personal luggage max weight is not exceeded
        if($this->currentPersonalLuggageWeight + $this->luggage->getWeight() > $this->maxPersonalLuggageWeight) {
            $this->addError('Het maximale gewicht van de persoonlijke bagage is overschreden, het totale maximale gewicht per persoon is ' . $this->maxPersonalLuggageWeight . ' kg');
        }

        // Validate if the total flight luggage weight is not exceeded
        if($this->currentFlightLuggageWeight + $this->luggage->getWeight() > $this->maxFlightLuggageWeight) {
            $this->addError('Het maximale gewicht van de vlucht is overschreden, het totale maximale gewicht per vlucht is ' . $this->maxFlightLuggageWeight . ' kg, het huidige gewicht is ' . $this->currentFlightLuggageWeight . ' kg. Ruimte over: ' . ($this->maxFlightLuggageWeight - $this->currentFlightLuggageWeight) . ' kg');
        }
    }    
}