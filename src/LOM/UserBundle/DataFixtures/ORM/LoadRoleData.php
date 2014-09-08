<?php

namespace LOM\UserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use LOM\UserBundle\Entity\Role;

class LoadRoleData extends AbstractFixture implements OrderedFixtureInterface {

  public function load(ObjectManager $manager) {
    $adminRole = $this->buildRole(
            'ROLE_ADMIN', 
            'ROLE_ADMIN', 
            'Super user'
            );
    $manager->persist($adminRole);
    $this->setReference("admin-role", $adminRole);
            
    $plnAdminRole = $this->buildRole(
            "ROLE_PLNADMIN", 
            'ROLE_PLNADMIN', 
            'Add new PLNs, content owners, add users to PLNs.', 
            $adminRole
            );
    $manager->persist($plnAdminRole);

    $depRole = $this->buildRole(
            'ROLE_DEPOSITOR', 
            'ROLE_DEPOSITOR', 
            "Depositors can add deposits to any PLN.", 
            $plnAdminRole
            );
    $manager->persist($depRole);

    $monRole = $this->buildRole(
            'ROLE_MONITOR',
            'ROLE_MONITOR',
            'Monitors can check the status of any PLN.',
            $depRole
            );
    $manager->persist($monRole);

    $userRole = $this->buildRole(
            'ROLE_USER',
            'ROLE_USER',
            'General users of the system.',
            $monRole
            );
    $manager->persist($userRole);
    
    $manager->flush();
  }

  private function buildRole($name, $role, $desc, Role $parent = null) {
    $role = new Role();
    $role->setName($name);
    $role->setRole($role);
    $role->setDescription($desc);
    if($parent !== null) {
        $role->setParent($parent);
    }
    return $role;
  }

    public function getOrder() {
        return 1;
    }

}
