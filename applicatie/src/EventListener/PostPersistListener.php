<?php

namespace App\EventListener;

use RWFramework\Framework\Dbal\Event\PostPersist;
use RWFramework\Framework\Http\Event\ResponseEvent;

class PostPersistListener {
    public function __invoke(PostPersist $event) {
        $entity = $event->getEntity();
    }
}