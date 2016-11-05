<?php
namespace Aristos\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @ORM\Entity(repositoryClass="Aristos\CoreBundle\Entity\Repository\CommentRepository")
 * @ORM\Table(name="queue")
 * @ORM\HasLifecycleCallbacks
 */
class Queue
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

   
    /**
     * @ORM\Column(type="boolean")
     * finished successfully or not
     */
    protected $status = false;
    
    /**
     * @ORM\Column(type="boolean")
     * processed and finished
     */
    protected $processed = false;
    
    /**
     * @ORM\Column(type="boolean")
     * skipped and will not be processed
     */
    protected $skipped = false;

    /**
     * @ORM\Column(type="text")
     */
    protected $task;
    

    /**
     * @ORM\Column(type="text")
     */
    protected $brand;
    
    /**
     * @ORM\Column(type="text")
     */
    protected $params;
    
   
       
    /**
     * @ORM\Column(type="datetime")
     */
    protected $createDate;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $updateDate;
    
  
    public function __construct()
    {
    
    	//doctring requires this
    	//$this->comments = new ArrayCollection();
    	 
    	$this->setCreateDate(new \DateTime());
    	$this->setUpdateDate(new \DateTime());
    
    	
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
     * Set status
     *
     * @param boolean $status
     * @return Queue
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return boolean 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set processed
     *
     * @param boolean $processed
     * @return Queue
     */
    public function setProcessed($processed)
    {
        $this->processed = $processed;

        return $this;
    }

    /**
     * Get processed
     *
     * @return boolean 
     */
    public function getProcessed()
    {
        return $this->processed;
    }

    /**
     * Set skipped
     *
     * @param boolean $skipped
     * @return Queue
     */
    public function setSkipped($skipped)
    {
        $this->skipped = $skipped;

        return $this;
    }

    /**
     * Get skipped
     *
     * @return boolean 
     */
    public function getSkipped()
    {
        return $this->skipped;
    }

    /**
     * Set brand
     *
     * @param string $brand
     * @return Queue
     */
    public function setBrand($brand)
    {
        $this->brand = $brand;

        return $this;
    }

    /**
     * Get brand
     *
     * @return string 
     */
    public function getBrand()
    {
        return $this->brand;
    }

    /**
     * Set params
     *
     * @param string $params
     * @return Queue
     */
    public function setParams($params)
    {
        $this->params = $params;

        return $this;
    }

    /**
     * Get params
     *
     * @return string 
     */
    public function getParams()
    {
        return $this->params;
    }
   

    /**
     * Set task
     *
     * @param string $task
     * @return Queue
     */
    public function setTask($task)
    {
        $this->task = $task;

        return $this;
    }

    /**
     * Get task
     *
     * @return string 
     */
    public function getTask()
    {
        return $this->task;
    }

    /**
     * Set createDate
     *
     * @param \DateTime $createDate
     * @return Queue
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
     * Set updateDate
     *
     * @param \DateTime $updateDate
     * @return Queue
     */
    public function setUpdateDate($updateDate)
    {
        $this->updateDate = $updateDate;

        return $this;
    }

    /**
     * Get updateDate
     *
     * @return \DateTime 
     */
    public function getUpdateDate()
    {
        return $this->updateDate;
    }
}
