<?php
namespace Aristos\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="Aristos\CoreBundle\Entity\Repository\TagRepository")
 * @ORM\Table(name="tag")
 * @ORM\HasLifecycleCallbacks()
 */
class Tag
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @ORM\Column(type="string")
     */
    protected $name;
    
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $description;
    
      
  /**
     * @ORM\Column(type="boolean")
     */
    protected $approved = false;
    

    /**
     * @ORM\Column(type="integer")
     * times this tag was used in questions and encounter questions
     */
    protected $timesUsed = 0;
    

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
     * @return Tag
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
     * Set description
     *
     * @param string $description
     * @return Tag
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
     * Set approved
     *
     * @param boolean $approved
     * @return Tag
     */
    public function setApproved($approved)
    {
        $this->approved = $approved;

        return $this;
    }

    /**
     * Get approved
     *
     * @return boolean 
     */
    public function getApproved()
    {
        return $this->approved;
    }

    /**
     * Set timesUsed
     *
     * @param integer $timesUsed
     * @return Tag
     */
    public function setTimesUsed($timesUsed)
    {
        $this->timesUsed = $timesUsed;

        return $this;
    }

    /**
     * Get timesUsed
     *
     * @return integer 
     */
    public function getTimesUsed()
    {
        return $this->timesUsed;
    }
}
