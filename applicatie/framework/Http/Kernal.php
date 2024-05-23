<?php
// The kernal will handle a request and return a response

namespace RWFramework\Framework\Http;

use FastRoute\RouteCollector;

use function FastRoute\simpleDispatcher;

class Kernal {
 
    public function handle(Request $request): Response {

        // Create a dispatcher (Dispatcher = verzender)
        $dispatcher = simpleDispatcher(function(RouteCollector $routeCollector) {
            $routes = include BASE_PATH . '/routes/web.php';
            
            foreach($routes as $route) {
                $routeCollector->addRoute(...$route); // '...' Splat operator. Will take the array and spread it out as arguments
            }
        });

        // Dispatch a URI to obtain the route info
        $routeInfo = $dispatcher->dispatch( // Verzenden
            $request->getMethod(),
            $request->getPathInfo()
        );

        [$status, [$controller, $method], $vars] = $routeInfo; // Quick way to assign the values of an array to variables

        // Call the handler, provided by the route info, in order to create a response
        // call_user_func: PHP will map the input arguments and pass it to the function. This is called variable functions
        $response = call_user_func_array([new $controller, $method], $vars);

        return $response;
    }
}