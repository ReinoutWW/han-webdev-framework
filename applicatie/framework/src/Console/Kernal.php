<?php

namespace RWFramework\Framework\Console;

use Psr\Container\ContainerInterface;
use RWFramework\Framework\Console\Command\CommandInterface;

class Kernal {
    public function __construct(
        private ContainerInterface $container,
        private Application $application) {
    }

    public function handle(): int {
        // Register commands with the container
        $this->registerCommands();

        // Run the console application returning a status code
        $status = $this->application->run();

        // Return status code
        return $status;
    }

    /**
     * Will register framework-defined commands, plus user-defined classes
     * Scans the Command directory and registers all classes that implement CommandInterface
     */
    private function registerCommands(): void {
        // Standard PHP framework class
        $commandFiles = new \DirectoryIterator(__DIR__ . '/Command');
        $namespace = $this->container->get('base-commands-namespace');

        foreach($commandFiles as $commandFile) {
            if(!$commandFile->isFile()) {
                continue;
            }

            $command = $namespace.pathinfo($commandFile, PATHINFO_FILENAME);

            if(is_subclass_of($command, CommandInterface::class)) {
                $commandName = (new \ReflectionClass($command))->getProperty('name')->getDefaultValue();

                $this->container->add($commandName, $command); // command name (alias) + class (concrete)
            }
        }
    }
}