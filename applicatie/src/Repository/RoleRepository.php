<?php

namespace App\Repository;

use Doctrine\DBAL\Connection;

class RoleRepository {
    public function __construct(
        private Connection $connection
    ) {}

    public function getRoles(): array {
        $query = $this->connection->createQueryBuilder()
            ->select('role_name')
            ->from('userRoles');

        return $query->executeQuery()->fetchAllAssociative();
    }

    public function getRolesByUserId(int $userId): array {
        // Join tables:
        // userRoles has a column userId and roleId
        // table roles has a column id and name
        $query = $this->connection->createQueryBuilder()
            ->select('r.name as role_name')
            ->from('userRoles', 'ur')
            ->join('ur', 'roles', 'r', 'ur.roleId = r.id')
            ->where('ur.userId = :userId')
            ->setParameter('userId', $userId);

        return $query->executeQuery()->fetchAllAssociative();
    }
}