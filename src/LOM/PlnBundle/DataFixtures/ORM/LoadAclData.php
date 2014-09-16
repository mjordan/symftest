<?php

namespace LOM\PlnBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use LOM\UserBundle\Entity\User;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Symfony\Component\Security\Acl\Exception\AclNotFoundException;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;

class LoadAclData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{

    private $container;

    public function load(ObjectManager $manager)
    {
        $franklin = $this->getReference('pln-franklin');
        
        $admin = $this->getReference('admin-user');
        $plnAdmin = $this->getReference('plnadmin-user');
        $depositor = $this->getReference('depositor-user');
        $monitor = $this->getReference('monitor-user');
        $user = $this->getReference('user-user');
        
        $this->setReference('pln-franklin', $franklin);

        $this->setEntityAcls($plnAdmin, $franklin, array(MaskBuilder::MASK_OWNER));
        $this->setEntityAcls($depositor, $franklin, array(MaskBuilder::MASK_MASTER));
        $this->setEntityAcls($monitor, $franklin, array(MaskBuilder::MASK_VIEW));

        $this->setClassAcls($admin, 'LOM\\PlnBundle\\Entity\\Pln', array(MaskBuilder::MASK_CREATE));
        $this->setClassAcls($plnAdmin, 'LOM\\PlnBundle\\Entity\\Pln', array(MaskBuilder::MASK_CREATE));
        $this->setClassAcls($user, 'LOM\\PlnBundle\\Entity\\Pln', array());
    }

    private function setEntityAcls(User $user, $subject, $permissions = array())
    {
        $aclProvider = $this->container->get('security.acl.provider');
        $objectId = ObjectIdentity::fromDomainObject($subject);
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

    private function setClassAcls(User $user, $className, $permissions = array())
    {
        $aclProvider = $this->container->get('security.acl.provider');
        $objectId = new ObjectIdentity('class', $className);
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
        $acl->insertClassAce($securityId, $builder->get());
        $aclProvider->updateAcl($acl);
    }

    public function getOrder()
    {
        return 5;
    }

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

}
