<?php

namespace RWFramework\Framework\Routing;

use FastRoute\RouteCollector;
use FastRoute\Dispatcher;
use RWFramework\Framework\Http\HttpException;
use RWFramework\Framework\Http\HttpRequestMethodException;
use RWFramework\Framework\Http\Request;

use function FastRoute\simpleDispatcher;

// The router is responsible for dispatching a request to the correct controller and method
// It will be used in the frontdoor controller to dispatch the request
class Router implements RouterInterface {
    
    public function dispatch(Request $request): array
    {
        $routeInfo = $this->extractRouteInfo($request);
     
        // It can be an anonymous function, or a controller and method
        // Handler in this situation is:
        // A. An anonymous function
        // B. An array with a controller and method
        [$handler, $vars] = $routeInfo;

        // If the handler is an array, create an instance of the controller
        if(is_array($handler)) {
            [$controller, $method] = $handler;
            $handler = [new $controller, $method];
        }

        return [$handler, $vars];
    }

    private function extractRouteInfo(Request $request): array {
        // Create a dispatcher (Dispatcher = verzender)
        // simpleDispatcher is also responsible for telling if it's a match with the routes
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

        // Check the resultcode if the route is a match
        switch($routeInfo[0]) { // Check status
            case Dispatcher::FOUND:
                return [$routeInfo[1], $routeInfo[2]]; // routeHandler, vars 
            case Dispatcher::METHOD_NOT_ALLOWED:
                $allowedMethods = implode(', ', $routeInfo[1]); // Seperated by a comma
                $e = new HttpRequestMethodException("The allowed methods are $allowedMethods");
                $e->setStatusCode(405);
                throw $e;    
            default:
                $e = new HttpException('Route not found'); 
                $e->setStatusCode(404);
                throw $e;
        }
    }
}