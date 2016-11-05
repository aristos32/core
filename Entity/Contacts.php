<?php 
namespace Aristos\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity(repositoryClass="Aristos\CoreBundle\Entity\Repository\ContactsRepository")
 * @ORM\Table(name="contacts")
 */
class Contacts 
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
	public $contact_id;
    
    
    /**
     * @ORM\ManyToOne(targetEntity="Aristos\CoreBundle\Entity\User", inversedBy="currentContact")
     * logged user
     */
    public $currentUser;

    /**
     * @ORM\ManyToOne(targetEntity="Aristos\CoreBundle\Entity\User", inversedBy="otherContact")
     * contact of the logged user
     */
    public $otherUser;
    
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * id of contact userpublic $contact_user_id;
     */
    
    
    /**
     * @ORM\Column(type="datetime")
     */
    protected $createDate;
       
    /**
     * @ORM\Column(type="string", columnDefinition="ENUM('active','suspended','deleted')")
     * @Assert\NotBlank()
     * current status for this contact
     */
    public $status;
    
   
    public function __construct()
    {
    	$this->setStatus('active');
    	$this->setCreateDate(new \DateTime());
    }

   
    /**
     * Set status
     *
     * @param string $status
     * @return Contact
     */
    public function setStatus($status)
    {
        $this->status = $status;
    
        return $this;
    }

    /**
     * Get status
     *
     * @return string 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set createDate
     *
     * @param \DateTime $createDate
     * @return Contacts
     */
    public function setCreateDate($createDate)
    {
        $this->createDate = $createDate;
    
        return $this;
    }

    /**
     * Get createDate
     *
     * @return \DateTime 
     */
    public function getCreateDate()
    {
        return $this->createDate;
    }

   

    /**
     * Set contact_id
     *
     * @param integer $contactId
     * @return Contacts
     */
    public function setContactId($contactId)
    {
        $this->contact_id = $contactId;
    
        return $this;
    }

    /**
     * Get contact_id
     *
     * @return integer 
     */
    public function getContactId()
    {
        return $this->contact_id;
    }

    


    /**
     * Set currentUser
     *
     * @param \Aristos\CoreBundle\Entity\User $currentUser
     * @return Contacts
     */
    public function setCurrentUser(\Aristos\CoreBundle\Entity\User $currentUser = null)
    {
        $this->currentUser = $currentUser;

        return $this;
    }

    /**
     * Get currentUser
     *
     * @return \Aristos\CoreBundle\Entity\User 
     */
    public function getCurrentUser()
    {
        return $this->currentUser;
    }

    /**
     * Set otherUser
     *
     * @param \Aristos\CoreBundle\Entity\User $otherUser
     * @return Contacts
     */
    public function setOtherUser(\Aristos\CoreBundle\Entity\User $otherUser = null)
    {
        $this->otherUser = $otherUser;

        return $this;
    }

    /**
     * Get otherUser
     *
     * @return \Aristos\CoreBundle\Entity\User 
     */
    public function getOtherUser()
    {
        return $this->otherUser;
    }
}
