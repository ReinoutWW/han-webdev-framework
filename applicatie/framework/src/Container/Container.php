<?php

namespace RWFramework\Framework\Container;
use Psr\Container\ContainerInterface;
use RWFramework\Framework\Container\ContainerException;

// Service provider will hold instances of objects. You can: add, get or check if a service exists
// If you try to get a service that hasn't been added yet, it will try to resolve it by adding it
// If it can't resolve it, it will throw an exception

class Container implements ContainerInterface {

    private array $services = [];

    public function add(string $id, string|object $concrete = null)
    {
        if (null === $concrete) {
            if (!class_exists($id)) {
                throw new ContainerException("Service $id could not be added");
            }

            $concrete = $id;
        }

        $this->services[$id] = $concrete;
    }

    public function get(string $id)
    {
        if (!$this->has($id)) {
            if (!class_exists($id)) {
                throw new ContainerException("Service $id could not be resolved");
            }

            $this->add($id);
        }

        $object = $this->resolve($this->services[$id]);

        return $object;
    }

    public function has(string $id): bool
    {
        return array_key_exists($id, $this->services);
    }

    private function resolve($class) : object { // $class can be an object or a name of a class
        // 1. Instanciate a reflection class (dump and check)
        $reflectionClass = new \ReflectionClass($class);

        // 2. Use reflection to try to obtain a class constructor
        $constructor = $reflectionClass->getConstructor();

        // 3. If there is no constructor, return a new instance of the class
        if($constructor === null) {
            return $reflectionClass->newInstance();
        }

        // 4. Get the contructor parameters
        $constructorParameters = $constructor->getParameters();

        // 5. Obtain dependencies
        $classDependencies = $this->resolveClassDependencies($constructorParameters);

        // 6. Instanciate with dependencies
        $service = $reflectionClass->newInstanceArgs($classDependencies);

        // 7. Return the object
        return $service;
    }

    private function resolveClassDependencies(array $reflectionParameters): array {
          // 1. Initialize empty dependencies array (required by newInstanceArgs)
          $classDependencies = [];

          // 2. Try to locate and instantiate each parameter
          foreach ($reflectionParameters as $parameter) {
  
              // Get the parameter's ReflectionNamedType as $serviceType
              $serviceType = $parameter->getType();
  
              // Try to instantiate using the internal get method
              $service = $this->get($serviceType->getName());
  
              // Add to the array
              $classDependencies[] = $service;
          }
  
          // 3. Return
          return $classDependencies;
    }
}