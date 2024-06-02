<?php

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;

return new class {

    // We don't need to return the $schema object because it is passed by reference 
    public function up(Schema $schema): void {
        $tableUserRoles = $schema->createTable('userRoles');
        $tableUserRoles->addColumn('roleId', Types::INTEGER, ['length' => 100]);
        $tableUserRoles->addColumn('userId', Types::INTEGER, ['length' => 100]);
        $tableUserRoles->addUniqueConstraint(['roleId', 'userId'], 'unique_user_and_role');
        $tableUserRoles->setPrimaryKey(['roleId', 'userId']);
        $tableUserRoles->addForeignKeyConstraint('roles', ['roleId'], ['id'], ['onDelete' => 'CASCADE', 'onUpdate' => 'CASCADE']);
        $tableUserRoles->addForeignKeyConstraint('users', ['userId'], ['id'], ['onDelete' => 'CASCADE', 'onUpdate' => 'CASCADE']);
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