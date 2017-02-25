<?php

namespace Aristos\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Aristos\CoreBundle\Entity\User;

/**
 * @ORM\Entity(repositoryClass="Aristos\CoreBundle\Entity\Repository\InternalMessageRepository")
 * @ORM\Table(name="internalmessage")
 * @ORM\HasLifecycleCallbacks()
 *
 * Internal Messages between Users
 * 
 * 
 */
class InternalMessage
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
   /**
     * @ORM\ManyToOne(targetEntity="Aristos\CoreBundle\Entity\User", inversedBy="messageSender")
     * user that send the message
     */
    protected $sender;
    
    /**
     * @ORM\ManyToOne(targetEntity="Aristos\CoreBundle\Entity\User", inversedBy="messageReceiver")
     * user that received the message
     */
    protected $receiver;

    
	/**
	 * @ORM\Column(type="text")
	 * message 
	 * */
	protected  $message = '';
	
	/**
	 * @ORM\Column(type="boolean")
	 * has question owner seen this or not
	 */
	protected $seen = FALSE;

	/**
	 * @ORM\Column(type="datetime")
	 */
	protected $sentDate;

	public function __construct()
	{	
		$this->setSentDate(new \DateTime());
		
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
     * Set message
     *
     * @param string $message
     * @return InternalMessage
     */
    public function setMessage($message)
    {
        $this->message = $message;
    
        return $this;
    }

    /**
     * Get message
     *
     * @return string 
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set sentDate
     *
     * @param \DateTime $sentDate
     * @return InternalMessage
     */
    public function setSentDate($sentDate)
    {
        $this->sentDate = $sentDate;
    
        return $this;
    }

    /**
     * Get sentDate
     *
     * @return \DateTime 
     */
    public function getSentDate()
    {
        return $this->sentDate;
    }



    /**
     * Set seen
     *
     * @param boolean $seen
     * @return InternalMessage
     */
    public function setSeen($seen)
    {
        $this->seen = $seen;

        return $this;
    }

    /**
     * Get seen
     *
     * @return boolean 
     */
    public function getSeen()
    {
        return $this->seen;
    }

    

    /**
     * Set sender
     *
     * @param \Aristos\CoreBundle\Entity\User $sender
     * @return InternalMessage
     */
    public function setSender(\Aristos\CoreBundle\Entity\User $sender = null)
    {
        $this->sender = $sender;

        return $this;
    }

    /**
     * Get sender
     *
     * @return \Aristos\CoreBundle\Entity\User 
     */
    public function getSender()
    {
        return $this->sender;
    }

    /**
     * Set receiver
     *
     * @param \Aristos\CoreBundle\Entity\User $receiver
     * @return InternalMessage
     */
    public function setReceiver(\Aristos\CoreBundle\Entity\User $receiver = null)
    {
        $this->receiver = $receiver;

        return $this;
    }

    /**
     * Get receiver
     *
     * @return \Aristos\CoreBundle\Entity\User 
     */
    public function getReceiver()
    {
        return $this->receiver;
    }
}
