<?php

namespace App\EventListener;

use RWFramework\Framework\Http\Event\ResponseEvent;

class InternalErrorListener {
    // 5XX Server errors
    private const INTERNAL_ERROR_MIN_VALUE = 499;
    public function __invoke(ResponseEvent $event) {
        $status = $event->getResponse()->getStatus();
        
        if ($status > self::INTERNAL_ERROR_MIN_VALUE) {
            $event->stopPropagation();
        }
    }
}