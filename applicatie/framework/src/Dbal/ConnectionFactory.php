<?php

namespace RWFramework\Framework\Dbal;

use Doctrine\DBAL\DriverManager;

/**
 * Just expects a database URL and will create a new connection
 * Easy datbase switch by just changing the URL
 */
class ConnectionFactory {
    public function __construct(private string $databaseUrl) {
    }

    public function create(): \Doctrine\DBAL\Connection {
        $connectionParams = array(
            'user' => 'sa',
            'password' => 'abc123!@#',
            'host' => 'database_server',
            'post' => 1434,
            'dbname' => 'GelreAirport',
            'driver' => 'pdo_sqlsrv',
            'driverOptions' => [
                'Encrypt' => '0',
            ]
        );

        return DriverManager::getConnection(
            $connectionParams
        );
    }
}
