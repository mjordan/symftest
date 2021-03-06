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

namespace LOM\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Users of the system.
 *
 * @ORM\Table(name="lom_users")
 * @ORM\Entity(repositoryClass="LOM\UserBundle\Entity\UserRepository")
 * @UniqueEntity(fields="username", message="Email address already used")
 */
class User implements UserInterface, \Serializable, EquatableInterface
{
    /**
     * User's ID.
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * We will use email addresses for user names.
     *
     * @ORM\Column(type="string", length=128, unique=true)
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    private $username;

    /**
     * Hashed password.
     *
     * @ORM\Column(type="string", length=64)
     */
    private $password;

    /**
     * Salt for the password.
     * @ORM\Column(type="string", length=32, nullable=true)
     */
    private $salt;

    /**
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;

    /**
     * @ORM\ManyToMany(targetEntity="Role", inversedBy="users")
     */
    private $roles;

    /**
     * @ORM\Column(name="fullname", type="string", length=128)
     */
    private $fullname;

    /**
     * @ORM\Column(name="institution", type="string", length=128)
     */
    private $institution;

    /**
     * @ORM\Column(name="reset_expires", type="datetime", nullable=true)
     */
    private $resetExpires;

    /**
     * @ORM\Column(name="reset_code", type="string", length=64, nullable=true)
     */
    private $resetCode;

    /**
     * Construct a user.
     */
    public function __construct()
    {
        $this->isActive = true;
        $this->roles = new ArrayCollection();
    }

    /**
     * Get a string representation of the user.
     *
     * @return type
     */
    public function __toString()
    {
        return (string) $this->username;
    }

    /**
     * Does nothing.
     */
    public function eraseCredentials()
    {
    }

    /**
     * Get the user's hashed password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Get the user's roles
     *
     * @return Role[]
     */
    public function getRoles()
    {
        return $this->roles->toArray();
    }

    /**
     * Get the user's hash salt.
     *
     * @return null
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * Get the user's username (which is an email address).
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Return a serialized version of the user
     *
     * @return string serialized user
     */
    public function serialize()
    {
        return serialize(array($this->id));
    }

    /**
     * Build a user object from a serialized representation.
     *
     * @param type $serialized
     */
    public function unserialize($serialized)
    {
        list($this->id) = unserialize($serialized);
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
     * Set username
     *
     * @param string $username
     *
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Set salt
     *
     * @param string $salt
     *
     * @return User
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;
    }

    /**
     * Generate a salt for the user.
     */
    public function generateSalt()
    {
        $this->salt = md5(uniqid());
    }

    /**
     * Set password
     *
     * @param string $password
     *
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     *
     * @return User
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Compare this user to another for equality.
     *
     * @param \Symfony\Component\Security\Core\User\UserInterface $user
     *
     * @return boolean
     */
    public function isEqualTo(UserInterface $user)
    {
        return $user !== null && $user->id === $this->id;
    }

    /**
     * Add roles
     *
     * @param \LOM\UserBundle\Entity\Role $roles
     *
     * @return User
     */
    public function addRole(\LOM\UserBundle\Entity\Role $roles)
    {
        $this->roles[] = $roles;

        return $this;
    }

    /**
     * Remove roles
     *
     * @param \LOM\UserBundle\Entity\Role $roles
     */
    public function removeRole(\LOM\UserBundle\Entity\Role $roles)
    {
        $this->roles->removeElement($roles);
    }

    /**
     * Set fullname
     *
     * @param string $fullname
     *
     * @return User
     */
    public function setFullname($fullname)
    {
        $this->fullname = $fullname;

        return $this;
    }

    /**
     * Get fullname
     *
     * @return string
     */
    public function getFullname()
    {
        return $this->fullname;
    }

    /**
     * Set institution
     *
     * @param string $institution
     *
     * @return User
     */
    public function setInstitution($institution)
    {
        $this->institution = $institution;

        return $this;
    }

    /**
     * Get institution
     *
     * @return string
     */
    public function getInstitution()
    {
        return $this->institution;
    }

    /**
     * Set reset_expires
     *
     * @param \DateTime $resetExpires
     *
     * @return User
     */
    public function setResetExpires($resetExpires)
    {
        $this->resetExpires = $resetExpires;

        return $this;
    }

    /**
     * Get reset_expires
     *
     * @return String
     */
    public function getResetExpires()
    {
        if (null === $this->resetExpires) {
            return null;
        }

        return $this->resetExpires->format(\DateTime::RFC850);
    }

    /**
     * Set reset_code
     *
     * @param string $resetCode
     *
     * @return User
     */
    public function setResetCode($resetCode)
    {
        $this->resetCode = $resetCode;

        return $this;
    }

    /**
     * Get reset_code
     *
     * @return string
     */
    public function getResetCode()
    {
        return $this->resetCode;
    }
}
