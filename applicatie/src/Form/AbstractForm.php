<?php

namespace App\Form;

abstract class AbstractForm {
    private array $errors = [];
    
    public function hasValidationErrors(): bool {
        return count($this->getValidationErrors()) > 0;
    }

    public function getValidationErrors(): array {
        // For performance reasons, only run validation if there are no errors yet
        if(!empty($this->errors)) {
            return $this->errors;
        }

        $this->validate();

        return $this->errors;
    }

    protected function addError(string $error): void {
        $this->errors[] = $error;
    }

    protected abstract function validate() : void;
}