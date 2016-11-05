<?php 
namespace Aristos\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity(repositoryClass="Aristos\CoreBundle\Entity\Repository\ContactRequestRepository")
 * @ORM\Table(name="contactrequest")
 */
class ContactRequest
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
	public $contact_request_id;
    
   
    

    /**
     * @ORM\ManyToOne(targetEntity="Aristos\CoreBundle\Entity\User", inversedBy="senderContactRequest")
     * field question_id will be created automatically in the table when running command
     * php app/console doctrine:schema:update --force
     */
    public $senderUser;
    
    /**
     * @ORM\ManyToOne(targetEntity="Aristos\CoreBundle\Entity\User", inversedBy="receiverContactRequest")
     * field question_id will be created automatically in the table when running command
     * php app/console doctrine:schema:update --force
     */
    public $receiverUser;
    
    /**
     * @ORM\Column(type="datetime")
     */
    protected $createDate;
       
    /**
     * @ORM\Column(type="string", columnDefinition="ENUM('new','viewed', 'accepted', 'postponed', 'deleted')")
     * @Assert\NotBlank()
     * current status for this contact
     */
    public $status;
    
   

    public function __construct()
    {    	   	 
    	$this->setCreateDate(new \DateTime());
    	$this->setStatus('new');
    }
   

  
    /**
     * Set createDate
     *
     * @param \DateTime $createDate
     * @return ContactRequest
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
     * Set status
     *
     * @param string $status
     * @return ContactRequest
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
     * Set contact_request_id
     *
     * @param integer $contactRequestId
     * @return ContactRequest
     */
    public function setContactRequestId($contactRequestId)
    {
        $this->contact_request_id = $contactRequestId;
    
        return $this;
    }

    /**
     * Get contact_request_id
     *
     * @return integer 
     */
    public function getContactRequestId()
    {
        return $this->contact_request_id;
    }

   

    /**
     * Set senderUser
     *
     * @param \Aristos\CoreBundle\Entity\User $senderUser
     * @return ContactRequest
     */
    public function setSenderUser(\Aristos\CoreBundle\Entity\User $senderUser = null)
    {
        $this->senderUser = $senderUser;

        return $this;
    }

    /**
     * Get senderUser
     *
     * @return \Aristos\CoreBundle\Entity\User 
     */
    public function getSenderUser()
    {
        return $this->senderUser;
    }

    /**
     * Set receiverUser
     *
     * @param \Aristos\CoreBundle\Entity\User $receiverUser
     * @return ContactRequest
     */
    public function setReceiverUser(\Aristos\CoreBundle\Entity\User $receiverUser = null)
    {
        $this->receiverUser = $receiverUser;

        return $this;
    }

    /**
     * Get receiverUser
     *
     * @return \Aristos\CoreBundle\Entity\User 
     */
    public function getReceiverUser()
    {
        return $this->receiverUser;
    }
}
