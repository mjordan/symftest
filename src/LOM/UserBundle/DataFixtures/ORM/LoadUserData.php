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

class LoadUserData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface {

    private $container;
    
    public function setContainer(ContainerInterface $container = null) {
        $this->container = $container;
    }
    
    public function load(ObjectManager $manager) {
        $user = new User();
        $user->setUsername("admin@example.com");
        $user->setFullname("Admin");
        $user->setIsActive(true);
        $user->addRole($this->getReference("admin-role"));
        $user->setInstitution("");
        
        $encoder = $this->container
                ->get('security.encoder_factory')
                ->getEncoder($user);
        $user->setPassword($encoder->encodePassword('supersecret', $user->getSalt()));
        
        $manager->persist($user);
        $manager->flush();
    }

    public function getOrder() {
        return 2;
    }

}
