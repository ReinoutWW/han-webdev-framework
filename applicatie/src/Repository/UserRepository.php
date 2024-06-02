<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\DBAL\Connection;
use RWFramework\Framework\Authentication\AuthRepositoryInterface;
use RWFramework\Framework\Authentication\AuthUserInterface;

/**
 * The UserRepository class is responsible for providing a collection-like interface for working with user records in the database.
 * It provides methods and properties for working with user records.
 * No go: The UserRepository class should not interact with the database directly. Instead, it should delegate this responsibility to a separate class. (e.g. DataMapper)
 */
class UserRepository implements AuthRepositoryInterface {
    public function __construct(private Connection $connection)
    {
    }
    
    public function findByUsername(string $username): ?AuthUserInterface
    {
        // Create a query builder
        $queryBuilder = $this->connection->createQueryBuilder();

        $queryBuilder
            ->select('id', 'name', 'password', 'gender', 'created_at')
            ->from('users')
            ->where('name = :name')
            ->setParameter(':name', $username);

        $result = $queryBuilder->executeQuery();

        $row = $result->fetchAssociative();

        if (!$row) {
            return null;
        }

        $user = new User(
            id: $row['id'],
            name: $row['name'],
            password: $row['password'],
            gender: $row['gender'],
            createdAt: new \DateTimeImmutable($row['created_at'])
        );

        return $user;
    }
}