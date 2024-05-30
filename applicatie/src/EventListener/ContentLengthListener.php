<?php

namespace App\EventListener;

use RWFramework\Framework\Http\Event\ResponseEvent;

class ContentLengthListener
{
   public function __invoke(ResponseEvent $event) {
        $response = $event->getResponse();
        
        if (!array_key_exists('Content-Length', $response->getHeaders())) {
            $response->setHeader('Content-Length', strlen($response->getContent()));
        }
    }
}