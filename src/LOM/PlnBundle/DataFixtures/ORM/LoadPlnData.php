<?php

namespace LOM\PlnBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use LOM\PlnBundle\Entity\Pln;
use LOM\UserBundle\Entity\User;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;
use Symfony\Component\Security\Acl\Exception\AclNotFoundException;

class LoadPlnData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{

    private $container;

    public function getOrder()
    {
        return 3;
    }

    public function load(ObjectManager $manager)
    {
        $franklin = $this->buildPln('franklin', $manager);
        $this->setReference('pln-franklin', $franklin);
        
        $dewey = $this->buildPln('dewey', $manager);
        $this->setReference('pln-dewey', $dewey);
        
//        $borges = $this->buildPln('borges', $manager);
//        $this->setReference('pln-borges', $borges);
//
//        $larkin = $this->buildPln('larkin', $manager);
//        $this->setReference('pln-larkin', $larkin);
//
//        $jefferson = $this->buildPln('jeffeson', $manager);
//        $this->setReference('pln-jefferson', $jefferson);
        
        $manager->flush();
    }

    private function buildPln($name, ObjectManager $manager)
    {
        $pln = new Pln();
        $pln->setName($name);
        $manager->persist($pln);
        return $pln;
    }

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

}
