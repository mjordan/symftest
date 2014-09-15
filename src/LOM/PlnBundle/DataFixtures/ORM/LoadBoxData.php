<?php

namespace LOM\PlnBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\Common\Persistence\ObjectManager;
use LOM\PlnBundle\Entity\Box;

class LoadBoxData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{

    private $container;

    public function getOrder()
    {
        return 4;
    }
    
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
//        $this->buildBox('aaron', 'pln-borges', $manager);
        $this->buildBox('adrian', 'pln-dewey', $manager);
        $this->buildBox('alice', 'pln-franklin', $manager);
//        $this->buildBox('titus', 'pln-jefferson', $manager);
//        $this->buildBox('puck', 'pln-larkin', $manager);
//        $this->buildBox('ethel', 'pln-borges', $manager);
        $this->buildBox('barnardo', 'pln-dewey', $manager);
        $this->buildBox('bassianus', 'pln-franklin', $manager);
//        $this->buildBox('banquo', 'pln-jefferson', $manager);
//        $this->buildBox('duncan', 'pln-larkin', $manager);
//        $this->buildBox('benedick', 'pln-borges', $manager);
        $this->buildBox('cassio', 'pln-dewey', $manager);
        $this->buildBox('henry', 'pln-franklin', $manager);
//        $this->buildBox('arthur', 'pln-jefferson', $manager);
//        $this->buildBox('brabantio', 'pln-larkin', $manager);
//        $this->buildBox('brutus', 'pln-borges', $manager);
        $this->buildBox('burgundy', 'pln-dewey', $manager);
        $this->buildBox('caliban', 'pln-franklin', $manager);
//        $this->buildBox('ceres', 'pln-jefferson', $manager);
//        $this->buildBox('orlando', 'pln-larkin', $manager);
//        $this->buildBox('cladius', 'pln-borges', $manager);
        $this->buildBox('cressida', 'pln-dewey', $manager);
        $this->buildBox('troilus', 'pln-franklin', $manager);
//        $this->buildBox('cymbeline', 'pln-jefferson', $manager);
//        $this->buildBox('demetrius', 'pln-larkin', $manager);
//        $this->buildBox('dogberry', 'pln-borges', $manager);
        $this->buildBox('edward', 'pln-dewey', $manager);
        $this->buildBox('elenor', 'pln-franklin', $manager);
//        $this->buildBox('fleance', 'pln-jefferson', $manager);
        $manager->flush();
    }
    
    private function buildBox($hostname, $network, ObjectManager $manager) {
        $box = new Box();
        $box->setHostName($hostname);
        $box->setPln($this->getReference($network));
        $manager->persist($box);
        return $box;
    }

}