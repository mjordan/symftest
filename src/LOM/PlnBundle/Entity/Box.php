<?php

namespace LOM\PlnBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Box
 *
 * @ORM\Table(name="boxes")
 * @ORM\Entity(repositoryClass="LOM\PlnBundle\Entity\BoxRepository")
 */
class Box
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * 
     * @ORM\Column(name="hostname", type="string", length=128, unique=true)
     * @Assert\NotBlank()
     */
    private $hostname;

    /**
     * @ORM\ManyToOne(targetEntity="Pln", inversedBy="boxes")
     * @ORM\JoinColumn(name="pln_id", referencedColumnName="id")
     */
    private $pln;
    
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
     * Set hostname
     *
     * @param string $hostname
     * @return Box
     */
    public function setHostname($hostname)
    {
        $this->hostname = $hostname;

        return $this;
    }

    /**
     * Get hostname
     *
     * @return string 
     */
    public function getHostname()
    {
        return $this->hostname;
    }

    /**
     * Set pln
     *
     * @param \LOM\PlnBundle\Entity\Pln $pln
     * @return Box
     */
    public function setPln(\LOM\PlnBundle\Entity\Pln $pln = null)
    {
        $this->pln = $pln;

        return $this;
    }

    /**
     * Get pln
     *
     * @return \LOM\PlnBundle\Entity\Pln 
     */
    public function getPln()
    {
        return $this->pln;
    }
    
    public function __toString() {
        return $this->hostname;
    }
}
