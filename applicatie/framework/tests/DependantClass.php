<?php

namespace RWFramework\Framework\Tests;

class DependantClass {
    public function __construct(private DependancyClass $dependency) {

    }  

    public function getDependency(): DependancyClass
    {
        return $this->dependency;
    }
}