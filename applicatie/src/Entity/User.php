<?php

namespace App\Entity;
use RWFramework\Framework\Authentication\AuthUserInterface;
use RWFramework\Framework\Dbal\Entity;
use RWFramework\Framework\Http\Roles\RoleUserInterface;

class User extends Entity implements AuthUserInterface, RoleUserInterface {
    public function __construct(
        private ?int $id,
        private string $name,
        private string $password,
        private string $gender,
        private \DateTimeImmutable $createdAt,
        private array $roles = []
    ) {
    }

    public static function create(string $name, string $plainPassword, string $gender, array $roles = []): self {
        return new self(
            id: null, 
            name: $name,
            gender: $gender,
            password: password_hash($plainPassword, PASSWORD_DEFAULT),
            createdAt: new \DateTimeImmutable(),
            roles: $roles
        );
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

    public function getName(): string {
        return $this->name;
    }

    public function getPassword(): string {
        return $this->password;
    }

    public function getCreatedAt(): \DateTimeImmutable {
        return $this->createdAt;
    }

    public function getUsername(): string {
        return $this->name;
    }

    public function getGender(): string {
        return $this->gender;
    }

    public function getRoles(): array
    {
        return $this->roles[0] ?? [];
    }
}