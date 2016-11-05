<?php
namespace Aristos\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="Aristos\CoreBundle\Entity\Repository\HobbyRepository")
 * @ORM\Table(name="hobby")
 * @ORM\HasLifecycleCallbacks()
 * 
 * a hobby of a user
 */
class Hobby
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
    * admin approved
    * 
    * @ORM\Column(type="boolean")
    */
    protected $approved = false;
    

    /**
     * @ORM\ManyToOne(targetEntity="Aristos\CoreBundle\Entity\User", inversedBy="hobbies")
     *
     * owner of hobby
     *
     **/
    protected $user;
      

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
     * Set user
     *
     * @param \Aristos\CoreBundle\Entity\User $user
     * @return Hobby
     */
    public function setUser(\Aristos\CoreBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Aristos\CoreBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }
}
