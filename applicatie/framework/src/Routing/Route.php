<?php

namespace RWFramework\Framework\Routing;

use RWFramework\Framework\Http\Roles\RequiredRoles;

class Route {
    public function __construct(
        private string $methodType,
        private string $path,
        private mixed $controller,
        private array $middleware = [],
        private RequiredRoles $requiredRoles = new RequiredRoles()
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

    public function getRoles(): array {
        return $this->requiredRoles->getRequiredRoles();
    }
}