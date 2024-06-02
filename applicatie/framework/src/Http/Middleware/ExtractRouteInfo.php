<?php

namespace RWFramework\Framework\Http\Middleware;

use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use RWFramework\Framework\Http\HttpException;
use RWFramework\Framework\Http\HttpRequestMethodException;
use RWFramework\Framework\Http\Request;
use RWFramework\Framework\Http\Response;
use RWFramework\Framework\Http\Roles\RoleManagerInterface;

use function FastRoute\simpleDispatcher;

class ExtractRouteInfo implements MiddlewareInterface
{
    public function __construct(
        private array $routes,
        private RoleManagerInterface $roleManager
    )
    {
    }

    public function process(Request $request, RequestHandlerInterface $requestHandler): Response {
        // Create a dispatcher (Dispatcher = verzender)
        // simpleDispatcher is also responsible for telling if it's a match with the routes
        $dispatcher = simpleDispatcher(function(RouteCollector $routeCollector) {
            foreach($this->routes as $route) {
                $routeCollector->addRoute(
                    $route->getMethodType(),
                    $route->getPath(), 
                    [
                        $route->getController(),
                        $route->getMiddleware(),
                        $route->getRoles()
                    ],
                );
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
                // Set routeHandler 
                $request->setRouteHandler($routeInfo[1][0]); 

                // Set routeHandlerArgs
                $request->setRouteHandlerArgs($routeInfo[2]); 
                
                // Inject route middleware on handler
                if(is_array($routeInfo[1]) && isset($routeInfo[1][1])) {
                    $requestHandler->injectMiddleware($routeInfo[1][1]);
                }

                if(is_array($routeInfo[1]) && isset($routeInfo[1][2])) {
                    $this->roleManager->addRequiredRoles($routeInfo[1][2]);
                }
                
                break;
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

        return $requestHandler->handle($request);
    }
}