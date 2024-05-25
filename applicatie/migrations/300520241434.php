<?php

/**
 * Anonymous class for migration
 */

use Doctrine\DBAL\Schema\Schema;

return new class {
    public function up(Schema $schema): void {
        echo get_class($this) . ' up method is called' . PHP_EOL;
    }

    public function down(): void {
        echo get_class($this) . ' down method is called' . PHP_EOL;
    }
};