<?php

/* 
 * Copyright (C) Error: on line 4, column 33 in Templates/Licenses/license-gpl20.txt
The string doesn't match the expected date/time format. The string to parse was: "27-Aug-2014". The expected format was: "MMM d, yyyy". mjoyce
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

namespace LOM\UserBundle\Entity;

use Symfony\Component\Security\Core\Role\RoleInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Roles are the heart of the permissions system.
 *
 * @ORM\Table(name="lom_roles")
 * @ORM\Entity()
 * @UniqueEntity(fields="role", message="Roles must be unique.")
 */
class Role implements RoleInterface {

    /**
     * Role ID.
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * Name of the role. Must match ROLE_* and be identical to $role.
     *
     * @ORM\Column(name="name", type="string", length=20, unique=true)
     */
    private $name;

    /**
     * The actual role. Must match ROLE_* and be identical to $name.
     *
     * @ORM\Column(name="role", type="string", length=20, unique=true)
     */
    private $role;

    /**
     * Roles are hierarchical. This is the parent. The hierarchy is defined
     * and filed out by LOM\UserBundle\Security\RoleHierarchy.
     *
     * @ORM\ManyToOne(targetEntity="Role", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", nullable=true)
     */
    private $parent;

    /**
     * The children of this role.
     *
     * @ORM\OneToMany(targetEntity="Role", mappedBy="parent")
     * @ORM\joinColumn(name="id", referencedColumnName="parent_id")
     */
    private $children;

    /**
     * The users in this role.
     *
     * @ORM\ManyToMany(targetEntity="User", mappedBy="roles")
     */
    private $users;

    /**
     * The description fo the role.
     *
     * @ORM\Column(name="description", type="text")
     */
    private $description;

    /**
     * Get the role
     *
     * @return string
     */
    public function getRole() {
        return $this->role;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    /**
     * Return a string representation of the role.
     *
     * @return string
     */
    public function __toString() {
        return $this->role;
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * 
     * @return Role
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set role
     *
     * @param string $role
     *
     * @return Role
     */
    public function setRole($role)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Add users
     *
     * @param \LOM\UserBundle\Entity\User $users
     *
     * @return Role
     */
    public function addUser(\LOM\UserBundle\Entity\User $users)
    {
        $this->users[] = $users;

        return $this;
    }

    /**
     * Remove users
     *
     * @param \LOM\UserBundle\Entity\User $users
     */
    public function removeUser(\LOM\UserBundle\Entity\User $users)
    {
        $this->users->removeElement($users);
    }

    /**
     * Get users
     *
     * @return User[]
     */
    public function getUsers()
    {
        return $this->users->toArray();
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Role
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set parent
     *
     * @param \LOM\UserBundle\Entity\Role $parent
     *
     * @return Role
     */
    public function setParent(\LOM\UserBundle\Entity\Role $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return \LOM\UserBundle\Entity\Role 
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Add children
     *
     * @param \LOM\UserBundle\Entity\Role $children
     *
     * @return Role
     */
    public function addChild(\LOM\UserBundle\Entity\Role $children)
    {
        $this->children[] = $children;

        return $this;
    }

    /**
     * Remove children
     *
     * @param \LOM\UserBundle\Entity\Role $children
     */
    public function removeChild(Role $children)
    {
        $this->children->removeElement($children);
    }

    /**
     * Get children
     *
     * @return Role[]
     */
    public function getChildren()
    {
        return $this->children->toArray();
    }
}
