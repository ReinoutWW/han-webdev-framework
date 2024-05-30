<?php

namespace RWFramework\Framework\Dbal\Event;

use RWFramework\Framework\Dbal\Entity;
use RWFramework\Framework\EventDispatcher\Event;

class PostPersist extends Event {
    public function __construct(
        private Entity $subject
    ) { }

    public function getEntity(): Entity {
        return $this->subject;
    }
}