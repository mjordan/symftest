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

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use LOM\UserBundle\Entity\Role;

/**
 * Load the role fixtures into the database.
 */
class LoadRoleData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * Load the role data.
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $adminRole = $this->buildRole(
                'ROLE_ADMIN', 'ROLE_ADMIN', 'Super user'
        );
        $manager->persist($adminRole);
        $this->setReference("admin-role", $adminRole);

        $plnAdminRole = $this->buildRole(
                "ROLE_PLNADMIN", 'ROLE_PLNADMIN', 'Add new PLNs, content owners, add users to PLNs.', $adminRole
        );
        $manager->persist($plnAdminRole);
        $this->setReference('plnadmin-role', $plnAdminRole);

        $depRole = $this->buildRole(
                'ROLE_DEPOSITOR', 'ROLE_DEPOSITOR', "Depositors can add deposits to any PLN.", $plnAdminRole
        );
        $manager->persist($depRole);
        $this->setReference('depositor-role', $depRole);

        $monRole = $this->buildRole(
                'ROLE_MONITOR', 'ROLE_MONITOR', 'Monitors can check the status of any PLN.', $depRole
        );
        $manager->persist($monRole);
        $this->setReference('monitor-role', $monRole);

        $userRole = $this->buildRole(
                'ROLE_USER', 'ROLE_USER', 'General users of the system.', $monRole
        );
        $manager->persist($userRole);
        $this->setReference('user-role', $userRole);

        $manager->flush();
    }

    /**
     * Convenience method to build a role. Does not persist it to the database.
     *
     * @param string $name
     * @param string $role
     * @param string $desc
     * @param Role   $parent
     *
     * @return Role
     */
    private function buildRole($name, $role, $desc, Role $parent = null)
    {
        $role = new Role();
        $role->setName($name);
        $role->setRole($role);
        $role->setDescription($desc);
        if ($parent !== null) {
            $role->setParent($parent);
        }

        return $role;
    }

    /**
     * Roles must be loaded into the database before the users, so set the order
     * here.
     *
     * @return int
     */
    public function getOrder()
    {
        return 1;
    }

}
