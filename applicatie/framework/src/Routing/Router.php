<?php

namespace RWFramework\Framework\Routing;

use FastRoute\RouteCollector;
use FastRoute\Dispatcher;
use Psr\Container\ContainerInterface;
use RWFramework\Framework\Controller\AbstractController;
use RWFramework\Framework\Http\HttpException;
use RWFramework\Framework\Http\HttpRequestMethodException;
use RWFramework\Framework\Http\Request;

use function FastRoute\simpleDispatcher;

// The router is responsible for dispatching a request to the correct controller and method
// It will be used in the frontdoor controller to dispatch the request
class Router implements RouterInterface {
    private array $routes;

    public function dispatch(Request $request, ContainerInterface $container): array
    {
        $routeInfo = $this->extractRouteInfo($request);
     
        // It can be an anonymous function, or a controller and method
        // Handler in this situation is:
        // A. An anonymous function
        // B. An array with a controller and method
        [$handler, $vars] = $routeInfo;

        // If the handler is an array, create an instance of the controller
        if(is_array($handler)) {
            [$controllerId, $method] = $handler;
            $controller = $container->get($controllerId);

            // Make sure all controllers will have the request available. 
            // It could be added to the container as well, but then we will create a tight coupling between every instance that epends on the container. (Because container = available request)
            if(is_subclass_of($controller, AbstractController::class)) {
                $controller->setRequest($request);
            }

            $handler = [$controller, $method];
        }

        return [$handler, $vars];
    }

    public function setRoutes(array $routes): void
    {
        $this->routes = $routes;
    }

    private function extractRouteInfo(Request $request): array {
        // Create a dispatcher (Dispatcher = verzender)
        // simpleDispatcher is also responsible for telling if it's a match with the routes
        $dispatcher = simpleDispatcher(function(RouteCollector $routeCollector) {
            foreach($this->routes as $route) {
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