<?php

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;

return new class {

    // We don't need to return the $schema object because it is passed by reference 
    public function up(Schema $schema): void {
        $table = $schema->createTable('users');
        $table->addColumn('id', Types::INTEGER, ['autoincrement' => true, 'unsigned' => true]);
        $table->addColumn('name', Types::STRING, ['length' => 100]);
        $table->addColumn('email', Types::STRING, ['length' => 60]);
        $table->addColumn('password', Types::STRING, ['length' => 60]);
        $table->addColumn('gender', Types::STRING, ['length' => 3]);
        $table->addColumn('created_at', Types::DATETIME_IMMUTABLE, ['default' => 'CURRENT_TIMESTAMP']);
        $table->addUniqueConstraint(['email'], 'unique_email');
        $table->setPrimaryKey(['id']);
    }

    public function down(Schema $schema): void {
        // Drop the table
        if($schema->hasTable('users')) {
            $schema->dropTable('users');
        }
    }
};