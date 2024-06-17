<?php

namespace App\Form\User;

use App\Entity\User;
use App\Form\AbstractForm;
use App\Repository\UserMapper;
use App\Repository\UserRepository;

class RegistrationForm extends AbstractForm
{
    private string $name;
    private string $password;
    private string $gender;
    private string $repeatPassword;

    public function __construct(
        private UserMapper $userMapper,
        private UserRepository $userRepository
    ) {}

    public function setFields(string $name, string $gender, string $password, string $repeatPassword): void {
        $this->name = $name;
        $this->gender = $gender;
        $this->password = $password;
        $this->repeatPassword = $repeatPassword;
    }

    public function save(): User {
        $user = User::create(name: $this->name, plainPassword: $this->password, gender: $this->gender);

        $this->userMapper->save($user);

        return $user;
    }

    public function validate(): void {
        if($this->password !== $this->repeatPassword) {
            $this->addError('Wachtwoorden komen niet overeen.');
        }

        if($this->userRepository->usernameExists($this->name)) {
            $this->addError('De gebruikersnaam is al bezet. Kies een andere gebruikersnaam');
        }

        // name validation 
        // Characters, lengt, char type
        if (strlen($this->name) < 5 || strlen($this->name) > 20) {
            $this->addError('De gebruikersnaam moet tussen de 3 en 20 characters zijn.');
        }

        // password length
        if (strlen($this->password) < 8) {
            $this->addError('Het wachtwoord moet tussen de 8 en 20 characters zijn.');
        }

        if(strlen($this->gender) !== 1) {
            $this->addError('Het geslacht moet 1 character lang zijn.');
        }

    }
}