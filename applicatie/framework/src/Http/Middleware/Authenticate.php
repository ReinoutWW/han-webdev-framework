<?php

namespace RWFramework\Framework\Http\Middleware;

use RWFramework\Framework\Http\Request;
use RWFramework\Framework\Http\Response;

class Authenticate implements MiddlewareInterface {
    private bool $authenticated = true;

    public function process(Request $request, RequestHandlerInterface $handler): Response {
        if(!$this->authenticated) {
            return new Response("Not authenticated", 401);
        }

        return $handler->handle($request);
    }
}