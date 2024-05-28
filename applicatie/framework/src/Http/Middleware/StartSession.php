<?php 

namespace RWFramework\Framework\Http\Middleware;

use RWFramework\Framework\Http\Request;
use RWFramework\Framework\Http\Response;
use RWFramework\Framework\Session\SessionInterface;

class StartSession implements MiddlewareInterface {
    public function __construct(
        private SessionInterface $session,
        private string $apiPrefix = '/api') {

    }

    public function process(Request $request, RequestHandlerInterface $handler): Response {
        if(!str_starts_with($request->getPathInfo(), $this->apiPrefix)) {
            $this->session->start();
        }

        // We might need data from the session in the request later on in the middleware chain
        $request->setSession($this->session);

        return $handler->handle($request);
    }
}