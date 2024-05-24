<?php

namespace RWFramework\Framework\Routing;

use Psr\Container\ContainerInterface;
use RWFramework\Framework\Http\Request;

// All routers must implement this interface
interface RouterInterface {
    public function dispatch(Request $request, ContainerInterface $container): array;
    public function setRoutes(array $routes): void;
}