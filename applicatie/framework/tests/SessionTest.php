<?php

namespace RWFramework\Framework\Tests;

use PHPUnit\Framework\TestCase;

class SessionTest extends TestCase {
    protected function setUp(): void {
        unset($_SESSION);
    }

    
}