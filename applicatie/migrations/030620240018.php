<?php

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;

return new class {

    // We don't need to return the $schema object because it is passed by reference 
    public function up(Schema $schema): void {
        $tableRoles = $schema->createTable('roles');
        $tableRoles->addColumn('id', Types::INTEGER, ['autoincrement' => true, 'unsigned' => true]);
        $tableRoles->addColumn('name', Types::STRING, ['length' => 100]);
        $tableRoles->addUniqueConstraint(['name'], 'unique_role');
        $tableRoles->setPrimaryKey(['id']);
    }

    public function down(Schema $schema): void {
        // Drop all changes from above, in order with the constraints
        if($schema->hasTable('userRoles')) {
            $schema->dropTable('userRoles');
        }

        if($schema->hasTable('roles')) {
            $schema->dropTable('roles');
        }
    }
};