<?php
namespace Aristos\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="Aristos\CoreBundle\Entity\Repository\NotesRepository")
 * @ORM\Table(name="notes")
 * @ORM\HasLifecycleCallbacks()
 * 
 * notes related to an encounter question, used for trip planning
 */
class Note
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @ORM\Column(type="string")
     * 
     * title of note
     * 
     */
    protected $title;
    
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
     * @ORM\ManyToOne(targetEntity="Aristos\CoreBundle\Entity\User", inversedBy="notes")
     *
     * owner of note
     *
     **/
    protected $user;
      

    /**
     * @ORM\ManyToOne(targetEntity="\General\GeneralBundle\Entity\EncounterQuestion", inversedBy="notes")
     * 
     * question the note belongs to
     * 
     */
    protected $encounterquestion;
    
    /**
     * list points or bulleted comments
     * 
     * @ORM\OneToMany(targetEntity="\General\GeneralBundle\Entity\Comment", mappedBy="note", cascade={"remove"})
     */
    protected $points;
    
   
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
     * Set title
     *
     * @param string $title
     * @return Notes
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Notes
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
     * @return Notes
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
     * @return Notes
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

    /**
     * Set encounterquestion
     *
     * @param \General\GeneralBundle\Entity\EncounterQuestion $encounterquestion
     * @return Notes
     */
    public function setEncounterquestion(\General\GeneralBundle\Entity\EncounterQuestion $encounterquestion = null)
    {
        $this->encounterquestion = $encounterquestion;

        return $this;
    }

    /**
     * Get encounterquestion
     *
     * @return \General\GeneralBundle\Entity\EncounterQuestion 
     */
    public function getEncounterquestion()
    {
        return $this->encounterquestion;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->points = new \Doctrine\Common\Collections\ArrayCollection();
    }

   

    /**
     * Get points
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPoints()
    {
        return $this->points;
    }

    /**
     * Add points
     *
     * @param \General\GeneralBundle\Entity\Comment $points
     * @return Note
     */
    public function addPoint(\General\GeneralBundle\Entity\Comment $points)
    {
        $this->points[] = $points;

        return $this;
    }

    /**
     * Remove points
     *
     * @param \General\GeneralBundle\Entity\Comment $points
     */
    public function removePoint(\General\GeneralBundle\Entity\Comment $points)
    {
        $this->points->removeElement($points);
    }
}
