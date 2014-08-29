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

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Table(name="lom_users")
 * @ORM\Entity(repositoryClass="LOM\UserBundle\Entity\UserRepository")
 * @UniqueEntity(fields="username", message="Email address already used")
 */
class User implements UserInterface, \Serializable, EquatableInterface {

    /**
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
    private $reset_expires;

    /**
     * @ORM\Column(name="reset_code", type="string", length=64, nullable=true)
     */
    private $reset_code;

    public function save($obj) {
        $em = $obj->getDoctrine()->getEntityManager();
        $em->persist($this);
        $em->flush();
    }

    public function __construct() {
        $this->isActive = true;
        $this->roles = new ArrayCollection();
        $this->reset_expires = null;
    }

    public function __toString() {
        return $this->username;
    }

    public function eraseCredentials() {

    }

    public function getPassword() {
        return $this->password;
    }

    public function getRoles() {
        return $this->roles->toArray();
    }

    public function getSalt() {
        return null;
    }

    public function getUsername() {
        return $this->username;
    }

    public function serialize() {
        return serialize(array($this->id));
    }

    public function unserialize($serialized) {
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
     * @return User
     */
    public function setSalt($salt)
    {
    }

    /**
     * Set password
     *
     * @param string $password
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

    public function isEqualTo(UserInterface $user) {
        return $user !== null && $user->id === $this->id;
    }

    /**
     * Add roles
     *
     * @param \LOM\UserBundle\Entity\Role $roles
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
     * @return User
     */
    public function setResetExpires($resetExpires)
    {
        $this->reset_expires = $resetExpires;

        return $this;
    }

    /**
     * Get reset_expires
     *
     * @return \DateTime 
     */
    public function getResetExpires()
    {
        return $this->reset_expires;
    }

    /**
     * Set reset_code
     *
     * @param string $resetCode
     * @return User
     */
    public function setResetCode($resetCode)
    {
        $this->reset_code = $resetCode;

        return $this;
    }

    /**
     * Get reset_code
     *
     * @return string 
     */
    public function getResetCode()
    {
        return $this->reset_code;
    }
}
