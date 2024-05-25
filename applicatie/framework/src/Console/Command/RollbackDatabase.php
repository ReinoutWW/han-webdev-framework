<?php 

namespace RWFramework\Framework\Console\Command;

use Applicatie\Framework\Src\Console\Command\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class RollbackDatabase extends AbstractMigration implements CommandInterface{
    private string $name = 'database:migrations:rollback'; // Don't remove this line, it's used by the console

    public function __construct()
    {
    }

    // PHP_EOL = new line
    public function execute(array $params = []): int {
        return $this->processMigrations(AbstractMigration::MIGRATIONS_DOWN);
    }
}