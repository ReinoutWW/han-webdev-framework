<?php

namespace RWFramework\Framework\Http\Middleware;

use RWFramework\Framework\Http\Request;
use RWFramework\Framework\Http\Response;

interface MiddlewareInterface {
    public function process(Request $request, RequestHandlerInterface $handler): Response;
}