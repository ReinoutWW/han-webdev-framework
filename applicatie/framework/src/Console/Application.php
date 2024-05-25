<?php

namespace RWFramework\Framework\Console;

use Psr\Container\ContainerInterface;

class Application {
    public function __construct(private ContainerInterface $container) {

    }

    public function run(): int {
        // Get command name
        $argv = $_SERVER['argv'];

        $commandName = $argv[1] ?? null;
        
        // Throw exception if it doesnt exist
        if(!$commandName) {
            throw new ConsoleException('Please provide a command name');
        }

        // Use command name to obtain a command instance from the container
        $command = $this->container->get($commandName);

        // Parse variables to obtain command parameters
        $params = array_filter($argv, fn($arg) => strpos($arg, '--') === 0);
        $options = $this->parseOptions($params);

        // Execute the command
        $status = $command->execute($options);

        // Return the status code
        return $status;
    }

    private function parseOptions(array $params): array {
        $options = [];

        foreach($params as $param) {
            $option = explode('=', $param);
            $options[substr($option[0], 2)] = $option[1] ?? true;
        }

        return $options;
    }
}