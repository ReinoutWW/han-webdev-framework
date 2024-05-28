<?php

namespace App\Entity;

class User {
    public function __construct(
        private ?int $id,
        private string $username,
        private string $email,
        private string $password,
        private \DateTimeImmutable $createdAt
    ) {
    }

    public static function create(string $username, string $plainPassword, string $email): self {
        return new self(
            id: null, 
            username: $username, 
            password: password_hash($plainPassword, PASSWORD_DEFAULT),
            email: $email, 
            createdAt: new \DateTimeImmutable());
    }
}