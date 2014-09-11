<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
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
