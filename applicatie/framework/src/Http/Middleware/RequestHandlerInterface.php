<?php

namespace RWFramework\Framework\Http\Middleware;

use RWFramework\Framework\Http\Request;
use RWFramework\Framework\Http\Response;

interface RequestHandlerInterface {
    public function handle(Request $request): Response;
    public function injectMiddleware(array $middleware): void;
}