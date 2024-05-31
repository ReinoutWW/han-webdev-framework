<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\DBAL\Connection;
use RWFramework\Framework\Dbal\DataMapper;

/**
 * The UserMapper class is responsible for saving user records to the database.
 * It will communicate with the database directly to map and save user records.
 * No go: It should not contain any business logic or validation. (e.g. sending notifications, emails etc.)
 */
class UserMapper {
    public function __construct(
        private DataMapper $dataMapper
    ) {}

    public function save(User $user): void {
        $stmt = $this->dataMapper->getConnection()->prepare("
            INSERT INTO users (name, email, password, gender, created_at)
            VALUES (:name, :email, :password, :gender, :created_at)
        ");

        $stmt->bindValue(':name', $user->getName());
        $stmt->bindValue(':email', $user->getEmail());
        $stmt->bindValue(':password', $user->getPassword());
        $stmt->bindValue(':gender', $user->getGender());
        $stmt->bindValue(':created_at', $user->getCreatedAt()->format("Y-m-d H:i:s"));

        $stmt->executeStatement();

        $id = $this->dataMapper->save($user);

        $user->setId($id);
    }
}