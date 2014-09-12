<?php

namespace LOM\PlnBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Pln
 *
 * @ORM\Table(name="plns")
 * @ORM\Entity(repositoryClass="LOM\PlnBundle\Entity\PlnRepository")
 */
class Pln
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
     * @ORM\Column(name="name", type="string", length=128, unique=true)
     * @Assert\NotBlank()
     */
    private $name;
    
    /**
     * @ORM\OneToMany(targetEntity="Box", mappedBy="pln")
     */
    private $boxes;
    
    public function __construct() {
        $this->boxes = new ArrayCollection();
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
     * @return Pln
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
     * Add boxes
     *
     * @param \LOM\PlnBundle\Entity\Box $boxes
     * @return Pln
     */
    public function addBox(\LOM\PlnBundle\Entity\Box $boxes)
    {
        $this->boxes[] = $boxes;

        return $this;
    }

    /**
     * Remove boxes
     *
     * @param \LOM\PlnBundle\Entity\Box $boxes
     */
    public function removeBox(\LOM\PlnBundle\Entity\Box $boxes)
    {
        $this->boxes->removeElement($boxes);
    }

    /**
     * Get boxes
     *
     * @return Box[]
     */
    public function getBoxes()
    {
        return $this->boxes->toArray();
    }
    
    public function __toString() {
        return $this->name;
    }
}
