<?php

namespace RWFramework\Framework\Dbal;

use Doctrine\DBAL\Connection;
use RWFramework\Framework\Dbal\Event\PostPersist;
use RWFramework\Framework\EventDispatcher\EventDispatcher;

class DataMapper {
    public function __construct(
        private Connection $connection,
        private EventDispatcher $eventDispatcher
    ) { }

    public function getConnection(): Connection {
        return $this->connection;
    }

    public function save(Entity $subject): int|string|null {
        // Dispatch PostPersist event
        $this->eventDispatcher->dispatch(new PostPersist($subject));

        // Return lastInsertId
        return $this->connection->lastInsertId();
    }
}