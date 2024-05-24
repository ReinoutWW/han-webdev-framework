<?php

use League\Container\Argument\Literal\ArrayArgument;
use League\Container\Argument\Literal\StringArgument;
use League\Container\Container;
use League\Container\ReflectionContainer;
use RWFramework\Framework\Controller\AbstractController;
use RWFramework\Framework\Http\Kernal;
use RWFramework\Framework\Routing\Router;
use RWFramework\Framework\Routing\RouterInterface;
use Symfony\Component\Dotenv\Dotenv;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

$env = new Dotenv();
$env->load(BASE_PATH . '/.env');

$container = new Container();

// Reflections will read the code structure and will try to resolve the dependencies
$container->delegate(new ReflectionContainer(true));

# Parameters for the applciation
$routes = include BASE_PATH . '/routes/web.php';
$appEnv = $_SERVER['APP_ENV'];
$templatesPath = BASE_PATH . '/templates';

$container->add('APP_ENV', new StringArgument($appEnv));

# Services

// Add will create a new instance for every request (like addScoped() in C#?)
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

// addShared is like a addSingleton in C#?. It will use the same object for every request
// StringArgument is necessary because otherwise the container will try to instanciate the string

$container->addShared('filesystem-loader', FilesystemLoader::class)
    ->addArgument(new StringArgument($templatesPath));

$container->addShared('twig', Environment::class)
    ->addArgument('filesystem-loader');

$container->add(AbstractController::class);

// Inflector will be the final step before it's returned by the container. 
// For example, It's usefull if you want to call a method for every class that implements a specific interface
// In this specific case, we want to call the setContainer method for every class that extends AbstractController
// https://container.thephpleague.com/3.x/inflectors/
$container->inflector(AbstractController::class)
    ->invokeMethod('setContainer', [$container]);

return $container;