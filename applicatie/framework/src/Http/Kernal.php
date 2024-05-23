<?php
// The kernal will handle a request and return a response

namespace RWFramework\Framework\Http;

use RWFramework\Framework\Routing\Router;

class Kernal {
    public function __construct(private Router $router) { // PHP 8 will automatically create the property and assign the value
    }

    public function handle(Request $request): Response 
    {
        try {
            [$routeHandler, $vars] = $this->router->dispatch($request);
            
            // Call_user_func_array will call the function and pass the vars as arguments (No extra parse will be needed)
            $response = call_user_func_array($routeHandler, $vars); // Looks like an invoke in C#
        }
        catch(HttpException $exception) {
            $response = new Response($exception->getMessage(), $exception->getStatusCode());
        }
        catch(\Exception $exception) {
            $response = new Response('An error occurred', 500);
        }

        return $response;
    }
}