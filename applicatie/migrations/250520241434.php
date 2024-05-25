<?php

/**
 * Anonymous class for migration
 */
return new class {
    public function up(): void {
        echo get_class($this) . ' up method is called' . PHP_EOL;
    }

    public function down(): void {
        echo get_class($this) . ' down method is called' . PHP_EOL;
    }
};