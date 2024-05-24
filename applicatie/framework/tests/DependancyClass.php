<?php

namespace RWFramework\Framework\Tests;

class DependancyClass {
    public function __construct(private AnotherSubDependancy $subDependency)
    {
    }

    public function getSubDependency(): AnotherSubDependancy
    {
        return $this->subDependency;
    }
}