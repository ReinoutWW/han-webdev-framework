<?php

namespace App\Entity;
use RWFramework\Framework\Authentication\AuthUserInterface;
use RWFramework\Framework\Dbal\Entity;

class User extends Entity implements AuthUserInterface {
    public function __construct(
        private ?int $id,
        private string $username,
        private string $password,
        private \DateTimeImmutable $createdAt
    ) {
    }

    public static function create(string $username, string $plainPassword): self {
        return new self(
            id: null, 
            username: $username, 
            password: password_hash($plainPassword, PASSWORD_DEFAULT),
            createdAt: new \DateTimeImmutable());
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function setId(int $id): void {
        $this->id = $id;
    }

    public function getAuthId(): int
    {
        return $this->id;
    }

    public function getUsername(): string {
        return $this->username;
    }

    public function getPassword(): string {
        return $this->password;
    }

    public function getCreatedAt(): \DateTimeImmutable {
        return $this->createdAt;
    }
}