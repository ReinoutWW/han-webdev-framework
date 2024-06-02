<?php

namespace App\Form\User;

use App\Entity\User;
use App\Form\AbstractForm;
use App\Repository\UserMapper;

class RegistrationForm extends AbstractForm
{
    private string $name;
    private string $password;
    private string $gender;

    public function __construct(
        private UserMapper $userMapper
    ) {}

    public function setFields(string $name, string $gender, string $password): void {
        $this->name = $name;
        $this->gender = $gender;
        $this->password = $password;
    }

    public function save(): User {
        $user = User::create(name: $this->name, plainPassword: $this->password, gender: $this->gender);

        $this->userMapper->save($user);

        return $user;
    }

    public function validate(): void {
        // name validation 
        // Characters, lengt, char type
        if (strlen($this->name) < 5 || strlen($this->name) > 20) {
            $this->addError('name must be between 5 and 20 characters');
        }

        // password length
        if (strlen($this->password) < 8) {
            $this->addError('Password must be at least 8 characters');
        }
    }
}