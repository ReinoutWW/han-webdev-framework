<?php

namespace RWFramework\Framework\Console\Command;

use Applicatie\Framework\Src\Console\Command\AbstractMigration;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Psr\Container\ContainerInterface;

class MigrateDatabase extends AbstractMigration implements CommandInterface {
    private string $name = 'database:migrations:migrate'; // Don't remove this line, it's used by the console

    public function __construct()
    {
    }

    // PHP_EOL = new line
    public function execute(array $params = []): int {
        return $this->processMigrations(AbstractMigration::MIGRATIONS_UP);
    }
}