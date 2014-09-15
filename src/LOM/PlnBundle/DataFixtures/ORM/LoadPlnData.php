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
        $manager->flush();

        $this->setAcls($this->getReference('plnadmin-user'), $franklin, array(MaskBuilder::MASK_OWNER));
        $this->setAcls($this->getReference('depositor-user'), $franklin, array(MaskBuilder::MASK_MASTER));
        $this->setAcls($this->getReference('monitor-user'), $franklin, array(MaskBuilder::MASK_VIEW));

        $dewey = $this->buildPln('dewey', $manager);
        $this->setReference('pln-dewey', $dewey);
        $manager->flush();

//        $borges = $this->buildPln('borges', $manager);
//        $this->setReference('pln-borges', $borges);
//
//        $larkin = $this->buildPln('larkin', $manager);
//        $this->setReference('pln-larkin', $larkin);
//
//        $jefferson = $this->buildPln('jeffeson', $manager);
//        $this->setReference('pln-jefferson', $jefferson);
    }

    private function setAcls(User $user, Pln $pln, $permissions = array())
    {
        $aclProvider = $this->container->get('security.acl.provider');
        $objectId = ObjectIdentity::fromDomainObject($pln);
        try {
            $acl = $aclProvider->findAcl($objectId);
        } catch (AclNotFoundException $ex) {
            $acl = $aclProvider->createAcl($objectId);
        }
        $builder = new MaskBuilder();
        foreach ($permissions as $p) {
            $builder->add($p);
        }
        $securityId = UserSecurityIdentity::fromAccount($user);
        $acl->insertObjectAce($securityId, $builder->get());
        $aclProvider->updateAcl($acl);
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
