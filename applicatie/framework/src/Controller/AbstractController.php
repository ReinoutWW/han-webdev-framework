<?php

namespace RWFramework\Framework\Controller;

use Psr\Container\ContainerInterface;

abstract class AbstractController {
    protected ?ContainerInterface $container = null;
    
    public function setContainer(ContainerInterface $container) {
        $this->container = $container;
    }
}