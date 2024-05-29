<?php

namespace RWFramework\Framework\Routing;

class Route {
    public function __construct(
        private string $methodType,
        private string $path,
        private mixed $controller,
        private array $middleware = [],
    ) {}
 
    public function getMethodType(): string {
        return $this->methodType;
    }

    public function getPath(): string {
        return $this->path;
    }

    public function getController(): mixed {
        return $this->controller;
    }

    public function getMiddleware(): array {
        return $this->middleware;
    }
}