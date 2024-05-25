<?php

/**
 * Anonymous class for migration
 */

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;

return new class {
    public function up(Schema $schema): void {
        // Table creation here
        $table = $schema->createTable('posts');
        $table->addColumn('id', Types::INTEGER, ['autoincrement' => true, 'unsigned' => true]);
        $table->addColumn('title', Types::STRING, ['length' => 255]);
        $table->addColumn('body', Types::TEXT);
        $table->addColumn('created_at', Types::DATETIME_IMMUTABLE, ['default' => 'CURRENT_TIMESTAMP']);
        $table->setPrimaryKey(['id']);
    }

    public function down(): void {
        echo get_class($this) . ' down method is called' . PHP_EOL;
    }
};