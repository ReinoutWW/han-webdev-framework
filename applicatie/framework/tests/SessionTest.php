<?php

namespace RWFramework\Framework\Tests;

use PHPUnit\Framework\TestCase;
use RWFramework\Framework\Session\Session;

class SessionTest extends TestCase {
    protected function setUp(): void {
        unset($_SESSION);
    }

    public function testSetAndGetFlash() {
        // Pepare 
        $session = new Session();
        $session->setFlash("success", "Great job!");
        $session->setFlash("error", "It broke :( ");

        // Test if it's saved
        $this->assertTrue($session->hasFlash("success"));
        $this->assertTrue($session->hasFlash("error"));
   
        // Test if it's returned correctly
        $this->assertEquals($session->getFlash("success"), ["Great job!"]);
        $this->assertEquals($session->getFlash("error"), ["It broke :( "]); 
        $this->assertEquals($session->getFlash("error"), []); // Try to get the same message again (Should be empty array)
        $this->assertNotEquals($session->getFlash("this doesn't exist"), [""]);
    }
}