<?php

namespace LOM\UserBundle\Tests\Entity;

use LOM\UserBundle\Entity\User;

class UserTest extends \PHPUnit_Framework_TestCase {

    public function testDefaults() {
        $user = new User();
        $this->assertTrue($user->getIsActive());
        $this->assertInternalType('array', $user->getRoles());
    }
    
    public function testGenerateSalt() {
        $user = new User();
        $user->generateSalt();
        $this->assertRegexp('/^[0-9a-f]{32}$/', $user->getSalt());
    }
    
}