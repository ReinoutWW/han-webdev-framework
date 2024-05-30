<?php

use App\Provider\EventServiceProvider;

$provider = [
    EventServiceProvider::class
];

foreach($provider as $providerClass) {
    $provider = $container->get($providerClass);
    $provider->register();
}