<?php
// The kernal will handle a request and return a response

namespace RWFramework\Framework\Http;

use Doctrine\DBAL\Connection;
use Psr\Container\ContainerInterface;
use RWFramework\Framework\Routing\RouterInterface;

class Kernal {
    private string $appEnv;

    public function __construct(
        private RouterInterface $router,
        private ContainerInterface $container
    ) { // PHP 8 will automatically create the property and assign the value
        $this->appEnv = $container->get('APP_ENV');
    }

    public function handle(Request $request): Response 
    {
        try {
            [$routeHandler, $vars] = $this->router->dispatch($request, $this->container);
            
            // Call_user_func_array will call the function and pass the vars as arguments (No extra parse will be needed)
            $response = call_user_func_array($routeHandler, $vars); // Looks like an invoke in C#
        }
        catch(\Exception $exception) {
            $response = $this->createExceptionResponse($exception);
        }

        return $response;
    }

    /**
     * @throws \Exception $exception
     * This will handle the exception and return a response, instaed of creating exception handling *directly* in the frontdoor controller
     */
    private function createExceptionResponse(\Exception $exception): Response {
        if(in_array($this->appEnv, ['dev', 'test'])) {
            throw $exception;
        }

        if($exception instanceof HttpException) {
            return new Response($exception->getMessage(), $exception->getStatusCode());
        }

        return new Response('Server error', Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}