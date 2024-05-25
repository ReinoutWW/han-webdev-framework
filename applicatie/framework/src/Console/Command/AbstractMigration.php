<?php

namespace Applicatie\Framework\Src\Console\Command;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;

abstract class AbstractMigration {
    protected Connection $connection;
    protected string $migrationsPath;

    protected const MIGRATIONS_UP = 'up';
    protected const MIGRATIONS_DOWN = 'down';

    // Create setters for the connection and migrationsPath
    public function setConnection(Connection $connection): void {
        $this->connection = $connection;
    }

    public function setMigrationsPath(string $migrationsPath): void {
        $this->migrationsPath = $migrationsPath;
    }

    abstract function execute(array $params = []): int;

    protected function processMigrations(string $migrationDirection): int {
        try {
            // Create a migrations table SQL if table not already in existence
            $this->createMigrationsTable();

            $this->connection->beginTransaction();

            // Get $appliedMigrations which are already in the database.migrations table
            $apliedMigration = $this->getApliedMigrations();

            // Get the $migrationFiles from the migrations folder
            $migrationFiles = $this->getMigrationFiles();

            // Get the migrations to apply. i.e. they are in $migrationFiles but not in $appliedMigrations
            $migrationToApply = array_diff($migrationFiles, $apliedMigration);

            $schema = new Schema();

            // Create SQL for any migrations which have not been run ..i.e. which are not in the database
            foreach($migrationToApply as $migration) {
                // Require the object
                $migrationObject = require $this->migrationsPath . '/' . $migration;

                // Call direction method
                ($migrationDirection == $this::MIGRATIONS_DOWN) ? $migrationObject->down($schema) : $migrationObject->up($schema);

                // Add migration to database
                $this->insertMigration($migration);
            }

            // Execute the SQL query's
            $sqlArray = $schema->toSql($this->connection->getDatabasePlatform());

            foreach($sqlArray as $sql) {
                $this->connection->executeQuery($sql);
            }

            $this->connection->commit();

            return 0;   

        } catch(\Throwable $throwable) {
            $this->connection->rollBack();
            
            throw $throwable;
        }
    }

    protected function insertMigration(string $migration): void {
        // Placeholdr sql query (? = entry '1' which will be bound later on)
        $sql = 'INSERT INTO migrations (migration) VALUES (?)';

        // Prepare sql statement (Also against SQL injection)
        $stmt = $this->connection->prepare($sql);

        // Bind the value to the placeholder
        $stmt->bindValue(1, $migration);

        $stmt->executeStatement();
    }

    protected function getApliedMigrations(): array {
        $sql = 'SELECT migration FROM migrations';

        $appliedMigrations = $this->connection->executeQuery($sql)->fetchFirstColumn();

        return $appliedMigrations;
    }

    protected function getMigrationFiles(): array {
        $migrationFiles = scandir($this->migrationsPath);

        $filteredFiles = array_filter($migrationFiles, function($file) {
            return !in_array($file, ['.', '..']);
        });

        return $filteredFiles;
    }

    protected function createMigrationsTable(): void {
        $schemaManager = $this->connection->createSchemaManager();

        if(!$schemaManager->tablesExist('migrations')) {
            $schema = new Schema();
            $table = $schema->createTable('migrations');
            $table->addColumn('id', Types::INTEGER, ['autoincrement' => true]);
            $table->addColumn('migration', Types::STRING);
            $table->addColumn('created_at', Types::DATETIME_IMMUTABLE, ['default' => 'CURRENT_TIMESTAMP']);
            $table->setPrimaryKey(['id']);

            $sqlArray = $schema->toSql($this->connection->getDatabasePlatform());

            $this->connection->executeQuery($sqlArray[0]);
        
            echo 'Migrations table created' . PHP_EOL;
        } 
    }
}