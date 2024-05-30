<?php declare(strict_types=1);
// Using the frontdoor controller pattern
// This is the entry point of our application
// This file is responsible for receiving the request and sending the response
// It is the only file that is accessible from the outside world
// It's also a perfect example of encapsulation because it hides the complexity of the application

use RWFramework\Framework\Http\Kernal;
use RWFramework\Framework\Http\Request;

// __DIR__ will take the root of the project
define('BASE_PATH', dirname(__DIR__)); 

require_once BASE_PATH . '/vendor/autoload.php';

$container = require BASE_PATH . '/config/services.php';

// Bootstrapping the application
require BASE_PATH . '/bootstrap/bootstrap.php';

// Request received
$request = Request::createFromGlobals();

// The containers knows how to create a Kernal
// The expected argument is a RouterInterface
// And that is connected to a concrete class called Router
// Then the circle is complete, and the Kernal is created
$kernal = $container->get(Kernal::class);

// Create response (string of content) using the Kernal, which will run the request handler, which will run the middleware, which will run the router :D
$response = $kernal->handle($request);

// Send the response to the client
$response->send();

// To prevent memory leaks / data that isn't usefull anymore, we need to 'terminate' the request (sounds like a dispose in C# :) )
// In this example we use it to clear the flash messages from the session
$kernal->terminate($request, $response);