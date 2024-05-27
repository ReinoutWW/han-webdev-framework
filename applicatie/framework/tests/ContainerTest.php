<?php 

namespace RWFramework\Framework\Tests;

require 'vendor/autoload.php';

use App\Controllers\HomeController;
use League\Container\Argument\Literal\StringArgument;
use League\Container\ReflectionContainer;
use PHPUnit\Framework\TestCase;
use RWFramework\Framework\Container\Container;
use RWFramework\Framework\Container\ContainerException;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

// Test class
// Also good for test-driven development

class ContainerTest extends TestCase {
    /** @test */
    public function a_service_can_be_retreived_from_the_container() {
        // Setup
        $container = new Container();

        // Act
        // id string, concrete class name string | object
        $container->add('dependant-class', DependantClass::class);

        // Assert
        $this->assertInstanceOf(DependantClass::class, $container->get('dependant-class'));
    }

    /** @test */
    public function a_ContainerException_is_thrown_if_a_service_cannot_be_found()
    {
        // Setup
        $container = new Container();

        // Verwacht een specifieke exception
        $this->expectException(ContainerException::class);

        // Do something
        $container->add('foobar');
    }

    /** @test */
    public function can_check_if_the_container_has_a_service(): void {
        $container = new Container(); 
        $container->add('dependant-class', DependantClass::class);

        $this->assertTrue($container->has('dependant-class'));
        $this->assertFalse($container->has('non-existant-service'));
    }


    // Autowiring will automatically resolve the dependencies of a class
    // It'll create an instance of the class and inject the dependencies
  /** @test */
  public function services_can_be_recursively_autowired()
  {
        $container = new Container();

        $dependantService = $container->get(DependantClass::class);

        $dependancyService = $dependantService->getDependency();

        $this->assertInstanceOf(DependancyClass::class, $dependancyService);
        $this->assertInstanceOf(AnotherSubDependancy::class, $dependancyService->getSubDependency());
  }

//   /** @test */
//   public function can_twig_be_retreived_from_container(): void {
//         $container = new \League\Container\Container();

//         $container->delegate(new ReflectionContainer(true));

//         $templatesPath = $container->get('twig-templates-path');
        
//         $container->addShared('filesystem-loader', FilesystemLoader::class)
//         ->addArgument(new StringArgument($templatesPath));
    
//         $container->addShared(Environment::class)
//             ->addArgument('filesystem-loader');

//         $this->assertInstanceOf(Environment::class, $container->get(Environment::class));
//   }
}