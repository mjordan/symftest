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

namespace LOM\UserBundle\Security\Role;

use Symfony\Component\Security\Core\Role\RoleHierarchy as RH;
use Doctrine\ORM\EntityManager;

/**
 * LOMUserBundle's RoleHierarchy. Since we store roles in the database, we also
 * need to store the hierarchy there as well. And since we do that, we need
 * a way to populate the role hierarchy.
 */
class RoleHierarchy extends RH
{
    /**
     * Entity manager to get stuff from the database.
     *
     * @var EntityManager $em
     */
    private $em;

    /**
     * Build the hierarchy
     *
     * @param array         $hierarchy default hierarchy (probably empty)
     * @param EntityManager $em        entity manager to get the roles.
     */
    public function __construct(array $hierarchy, EntityManager $em)
    {
        $this->em = $em;
        parent::__construct($this->buildRolesTree($hierarchy));
    }

    /**
     * Here we build an array with roles. It looks like a two-levelled tree - just
     * like original Symfony roles are stored in security.yml
     *
     * @param array $hierarchy
     *
     * @return array
     */
    private function buildRolesTree(array $hierarchy)
    {
        $roles = $this->em->createQuery('select r from LOMUserBundle:Role r')->execute();
        foreach ($roles as $role) {
            if ($role->getParent()) {
                if (!isset($hierarchy[$role->getParent()->getName()])) {
                    $hierarchy[$role->getParent()->getName()] = array();
                }
                $hierarchy[$role->getParent()->getName()][] = $role->getName();
            } else {
                if (!isset($hierarchy[$role->getName()])) {
                    $hierarchy[$role->getName()] = array();
                }
            }
        }

        return $hierarchy;
    }

}
