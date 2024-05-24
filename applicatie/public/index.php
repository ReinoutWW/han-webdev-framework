<?php declare(strict_types=1);
// Using the frontdoor controller pattern
// This is the entry point of our application
// This file is responsible for receiving the request and sending the response
// It is the only file that is accessible from the outside world
// It's also a perfect example of encapsulation because it hides the complexity of the application

use RWFramework\Framework\Http\Kernal;
use RWFramework\Framework\Http\Request;
use RWFramework\Framework\Routing\Router;

// __DIR__ will take the root of the project
define('BASE_PATH', dirname(__DIR__)); 

require_once BASE_PATH . '/vendor/autoload.php';

$container = require BASE_PATH . '/config/services.php';

// Request received
$request = Request::createFromGlobals();

// The containers knows how to create a Kernal
// The expected argument is a RouterInterface
// And that is connected to a concrete class called Router
// Then the circle is complete, and the Kernal is created
$kernal = $container->get(Kernal::class);

// // Send response (string of content)
$response = $kernal->handle($request);

$response->send();