<?php

namespace LOM\UserBundle\Tests\Entity;

use LOM\UserBundle\Entity\User;

/**
 * Unit test the User class.
 */
class UserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Construct a user, test the defaults.
     */
    public function testDefaults()
    {
        $user = new User();
        $this->assertTrue($user->getIsActive());
        $this->assertInternalType('array', $user->getRoles());
    }

    /**
     * test the salt generation.
     */
    public function testGenerateSalt()
    {
        $user = new User();
        $user->generateSalt();
        $this->assertRegexp('/^[0-9a-f]{32}$/', $user->getSalt());
    }

}
