<?php

namespace RWFramework\Framework\Http\Middleware;

use Psr\Container\ContainerInterface;
use RWFramework\Framework\Http\Request;
use RWFramework\Framework\Http\Response;

/**
 * Beautifull :) 
 * This requesthandler will handle the request and pass it through the middleware stack.
 * The middleware stack is a list of middleware classes that will be executed in order.
 * Some extra nerd info: We are using the PSR-15 RequestHandlerInterface here. (Someone defined this interface for us to use, and I'll gladly follow this great pattern)
 * https://www.php-fig.org/psr/psr-15/
 */
class RequestHandler implements RequestHandlerInterface {
    public function __construct(private ContainerInterface $container) {
    }

    /**
     * The middleware stack will be processed in order. (array_shift = FIFO)
     */
    private array $middleware = [
        StartSession::class, // Start the session
        Authenticate::class, // Check if the user is authenticated
        RouterDispatch::class // Should be the final item! (This will dispatch the route and call the handler)
    ];
    
    public function handle(Request $request): Response {
        // If there are no more middleware to process, return a default response.
        // A response should have been returned before the middleware stack is empty.
        if(empty($this->middleware)) {
            return new Response("Something went wrong processing the request. ", 500);
        }

        // Get the next middleware from the stack.
        $middlewareClass = array_shift($this->middleware);

        $middleware = $this->container->get($middlewareClass);

        // Create a new instance of the middleware and call its process method. (Following the interface)
        $response = $middleware->process($request, $this);

        return $response;
    }
}