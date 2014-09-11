<?php

/*
 * Copyright (C) 2014 mjoyce
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

namespace LOM\UserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\Common\Persistence\ObjectManager;
use LOM\UserBundle\Entity\User;

/**
 * Load user data fixtures.
 */
class LoadUserData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    /**
     * Store a fixture container to load the roles saved by the role fixture
     * loader.
     *
     * @var ContainerInterface $container
     */
    private $container;

    /**
     * Set the container
     *
     * @param ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * Load the user fixtures.
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $adminUser = $this->buildUser("admin@example.com", "Admin", "admin-role");
        $manager->persist($adminUser);

        $plnAdminUser = $this->buildUser("plnadmin@example.com", "PLN Admin", "plnadmin-role");
        $manager->persist($plnAdminUser);

        $depositorUser = $this->buildUser("depositor@example.com", "Depositor", "depositor-role");
        $manager->persist($depositorUser);

        $monitorUser = $this->buildUser("monitor@example.com", "Monitor", "monitor-role");
        $manager->persist($monitorUser);

        $user = $this->buildUser("user@example.com", "User", "user-role");
        $manager->persist($user);

        $manager->flush();
    }

    /**
     * Build a new user and return it.
     *
     * @param string $username the user name
     * @param string $fullname the full name
     * @param string $role     the role, as remembered in LoadRoleData
     *
     * @return User
     */
    private function buildUser($username, $fullname, $role)
    {
        $user = new User();
        $user->setUsername($username);
        $user->setFullname($fullname);
        $user->setIsActive(true);
        $user->addRole($this->getReference($role));
        $user->setInstitution("");
        $user->generateSalt();

        $encoder = $this->container
                ->get('security.encoder_factory')
                ->getEncoder($user);

        $user->setPassword($encoder->encodePassword('supersecret', $user->getSalt()));

        return $user;
    }

    /**
     * Users must be loaded after the roles, so set the order here.
     *
     * @return int
     */
    public function getOrder()
    {
        return 2;
    }

}
