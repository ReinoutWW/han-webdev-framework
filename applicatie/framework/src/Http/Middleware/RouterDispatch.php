<?php

namespace RWFramework\Framework\Http\Middleware;

use Psr\Container\ContainerInterface;
use RWFramework\Framework\Http\Request;
use RWFramework\Framework\Http\Response;
use RWFramework\Framework\Routing\Router;
use RWFramework\Framework\Session\SessionInterface;

// This should be the final step of the middleware chain
// This will dispatch the route and call the handler
// Then return the response to the client
class RouterDispatch implements MiddlewareInterface {
    public function __construct(
        private Router $router, 
        private ContainerInterface $container,
        private SessionInterface $session
    ) {} // Dependency injection (Router and Container

    public function process(Request $request, RequestHandlerInterface $handler): Response {
        [$routeHandler, $vars] = $this->router->dispatch($request, $this->container);

        // Save last visited route in session
        if($request->getMethod() === 'GET') {
            $this->session->setLastVisitedRoute($request->getPathInfo());
        }
            
        // Call_user_func_array will call the function and pass the vars as arguments (No extra parse will be needed)
        $response = call_user_func_array($routeHandler, $vars); // Looks like an invoke in C#

        return $response;
    }
}