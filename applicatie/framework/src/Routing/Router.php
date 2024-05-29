<?php

namespace RWFramework\Framework\Routing;

use Psr\Container\ContainerInterface;
use RWFramework\Framework\Controller\AbstractController;
use RWFramework\Framework\Http\Request;

// The router is responsible for dispatching a request to the correct controller and method
// It will be used in the frontdoor controller to dispatch the request
class Router implements RouterInterface {
    public function dispatch(Request $request, ContainerInterface $container): array
    {
        $routeHandler = $request->getRouteHandler();
        $routeHandlerArgs = $request->getRouteHandlerArgs();
     
        // It can be an anonymous function, or a controller and method
        // Handler in this situation is:
        // A. An anonymous function
        // B. An array with a controller and method

        // If the handler is an array, create an instance of the controller
        if(is_array($routeHandler)) {
            [$controllerId, $method] = $routeHandler;
            $controller = $container->get($controllerId);

            // Make sure all controllers will have the request available. 
            // It could be added to the container as well, but then we will create a tight coupling between every instance that epends on the container. (Because container = available request)
            if(is_subclass_of($controller, AbstractController::class)) {
                $controller->setRequest($request);
            }

            $routeHandler = [$controller, $method];
        }

        return [$routeHandler, $routeHandlerArgs];
    }
}