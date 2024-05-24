<?php

use League\Container\Argument\Literal\ArrayArgument;
use League\Container\Argument\Literal\StringArgument;
use League\Container\Container;
use League\Container\ReflectionContainer;
use RWFramework\Framework\Http\Kernal;
use RWFramework\Framework\Routing\Router;
use RWFramework\Framework\Routing\RouterInterface;
use Symfony\Component\Dotenv\Dotenv;

$env = new Dotenv();
$env->load(BASE_PATH . '/.env');

$container = new Container();

// Reflections will read the code structure and will try to resolve the dependencies
$container->delegate(new ReflectionContainer(true));

# Parameters for the applciation
$routes = include BASE_PATH . '/routes/web.php';
$appEnv = $_SERVER['APP_ENV'];

$container->add('APP_ENV', new StringArgument($appEnv));

# Services
$container->add(
    RouterInterface::class, 
    Router::class
);

// Once instanciated, make sure to call the setRoutes method
// This will configure the routes for the router
$container->extend(RouterInterface::class)
    ->addMethodCall('setRoutes', [new ArrayArgument($routes)]);

$container->add(Kernal::class)
    ->addArgument(RouterInterface::class)
    ->addArgument($container);

return $container;