<?php

use App\Repository\UserRepository;
use Applicatie\Framework\Src\Console\Command\AbstractMigration;
use Doctrine\DBAL\Connection;
use League\Container\Argument\Literal\ArrayArgument;
use League\Container\Argument\Literal\StringArgument;
use League\Container\Container;
use League\Container\ReflectionContainer;
use RWFramework\Framework\Authentication\SessionAuthentication;
use RWFramework\Framework\Console\Application;
use RWFramework\Framework\Controller\AbstractController;
use RWFramework\Framework\Dbal\ConnectionFactory;
use RWFramework\Framework\Http\Middleware\Dummay;
use RWFramework\Framework\Http\Middleware\extractRouteInfo;
use RWFramework\Framework\Http\Middleware\RequestHandlerInterface;
use RWFramework\Framework\Http\Middleware\RouterDispatch;
use RWFramework\Framework\Routing\Router;
use RWFramework\Framework\Routing\RouterInterface;
use RWFramework\Framework\Session\Session;
use RWFramework\Framework\Session\SessionInterface;
use RWFramework\Framework\Template\TwigFactory;
use Symfony\Component\Dotenv\Dotenv;

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
$databaseUrl = 'sqlite:///'. BASE_PATH . '/var/db.sqlite';

$container->add('base-commands-namespace', new StringArgument('RWFramework\\Framework\\Console\\Command\\'));

# Services

// Add will create a new instance for every request (like addScoped() in C#?)
$container->add(
    RouterInterface::class, 
    Router::class
);

$container->add(\RWFramework\Framework\Http\Kernal::class)
    ->addArguments([
        RouterInterface::class, 
        $container, 
        RequestHandlerInterface::class
]);

$container->add(
    RequestHandlerInterface::class, 
    \RWFramework\Framework\Http\Middleware\RequestHandler::class
)->addArgument($container);

$container->add(\RWFramework\Framework\Console\Kernal::class)
    ->addArguments([$container, Application::class]);

$container->addShared(
    SessionInterface::class, 
    Session::class
);

$container->add(RouterDispatch::class)
    ->addArguments([
        RouterInterface::class,
        $container
    ]);

// addShared is like a addSingleton in C#?. It will use the same object for every request
// StringArgument is necessary because otherwise the container will try to instanciate the string
$container->add('template-renderer', TwigFactory::class)
    ->addArguments([
        SessionInterface::class,
        new StringArgument($templatesPath)
    ]);

$container->addShared('twig', function() use ($container) {
    return $container->get('template-renderer')->create();
});

$container->add(AbstractController::class);

// Inflector will be the final step before it's returned by the container. 
// For example, It's usefull if you want to call a method for every class that implements a specific interface
// In this specific case, we want to call the setContainer method for every class that extends AbstractController
// https://container.thephpleague.com/3.x/inflectors/
$container->inflector(AbstractController::class)
    ->invokeMethod('setContainer', [$container]);

$container->add(ConnectionFactory::class)
    ->addArgument(new StringArgument($databaseUrl));

$container->addShared(Connection::class, function() use ($container): Connection {
    return $container->get(ConnectionFactory::class)->create();
});

// Add abstract controller
$container->add(AbstractMigration::class);

// Inflect the abstract controller to set the connection and the migrations path
$container->inflector(AbstractMigration::class)
    ->invokeMethod('setConnection', [Connection::class])
    ->invokeMethod('setMigrationsPath', [new StringArgument(BASE_PATH . '/migrations')]);

$container->add(Application::class)
    ->addArgument($container);

$container->add(SessionAuthentication::class)
    ->addArguments([
        UserRepository::class,
        SessionInterface::class
    ]);

$container->add(\RWFramework\Framework\Http\Middleware\ExtractRouteInfo::class)
    ->addArgument(new ArrayArgument($routes));

return $container;