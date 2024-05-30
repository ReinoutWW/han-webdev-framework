<?php

namespace App\Provider;

use App\EventListener\ContentLengthListener;
use App\EventListener\InternalErrorListener;
use App\EventListener\PostPersistListener;
use RWFramework\Framework\Dbal\Event\PostPersist;
use RWFramework\Framework\EventDispatcher\EventDispatcher;
use RWFramework\Framework\Http\Event\ResponseEvent;
use RWFramework\Framework\ServiceProvider\ServiceProviderInterface;

/**
 * The main responsibility of the ServiceProvider is to simplify the manage and register application services tasks
 * e.g. EventDispatcher
 */
class EventServiceProvider implements ServiceProviderInterface {

    private array $listen = [
        ResponseEvent::class => [
            InternalErrorListener::class,
            ContentLengthListener::class
        ],
        PostPersist::class => [
            PostPersistListener::class
        ]
    ];

    public function __construct(
        private EventDispatcher $dispatcher
    ) { }

    public function register(): void {
        // loop over each event in the listen array
        foreach ($this->listen as $eventName => $listeners) {
            // loop over each listener
            foreach (array_unique($listeners) as $listener) {
                // call eventDispatcher->addListener
                $this->dispatcher->addListener($eventName, new $listener());
            }
        }
    }
}