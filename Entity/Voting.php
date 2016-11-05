<?php

namespace Aristos\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;


/**
 * @ORM\Entity(repositoryClass="Aristos\CoreBundle\Entity\Repository\VotingRepository")
 * @ORM\Table(name="voting")
 * @ORM\HasLifecycleCallbacks()
 * Votes can be either from questions or answers.
 * 
 * Why is needed: to know which user voted for which question/answer to not allow to vote again.
 * 
 * @param user : logged, person voting for something
 * 
 */
class Voting
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @ORM\ManyToOne(targetEntity="Aristos\CoreBundle\Entity\User", inversedBy="votes")
     * 
     * person voting for something
     * 
     **/
    protected $user_voting;
    
    /**
     * @ORM\ManyToOne(targetEntity="Aristos\CoreBundle\Entity\User", inversedBy="votes")
     *
     * person receiving a vote
     *
     **/
    protected $user_receiving;
    
    /**
     * @ORM\ManyToOne(targetEntity="\General\GeneralBundle\Entity\Question", inversedBy="votes")
     */
    protected $question;
    
   /**
     * @ORM\ManyToOne(targetEntity="\General\GeneralBundle\Entity\EncounterQuestion", inversedBy="votes")
     */
    protected $encounterquestion;
    
    /**
     * @ORM\ManyToOne(targetEntity="\General\GeneralBundle\Entity\AnswerForQuestion", inversedBy="votes")
     */
    protected $answerforquestion;
       
    /**
     * @ORM\ManyToOne(targetEntity="\General\GeneralBundle\Entity\AnswerForEncounterQuestion", inversedBy="votes")
     */
    protected $answerforencounterquestion;
    
   /**
     * @ORM\Column(type="integer", nullable=true, options={"comment" = "Votes cast, like +1 or -1"})
     */
    protected $votes;
    
    /**
     * @ORM\Column(type="integer", nullable=true, options={"comment" = "Points given, like 100"})
     * sometimes can get some points not related to question or answer.
     * example: when register you  get 100 bonus points
     * 
     */
    protected $points;
    
    
    /**
     * @ORM\Column(type="string", columnDefinition="ENUM('over 1000', 'initial register')", nullable=true)
     * reputation type. 
     * examples: initial register, over 1000 etc
     */
    protected $type;
    
    /**
     * @ORM\Column(type="boolean")
     * has question owner seen this or not
     */
    protected $seen = FALSE;
    
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    public function __toString()
    {
    	return $this->getType();
    }
      

    /**
     * Set type
     *
     * @param string $type
     * @return Voting
     */
    public function setType($type)
    {
        $this->type = $type;
    
        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set votes
     *
     * @param integer $votes
     * @return Voting
     */
    public function setVotes($votes)
    {
        $this->votes = $votes;
    
        return $this;
    }

    /**
     * Get votes
     *
     * @return integer 
     */
    public function getVotes()
    {
        return $this->votes;
    }

    /**
     * Set points
     *
     * @param integer $points
     * @return Voting
     */
    public function setPoints($points)
    {
        $this->points = $points;
    
        return $this;
    }

    /**
     * Get points
     *
     * @return integer 
     */
    public function getPoints()
    {
        return $this->points;
    }

   

    /**
     * Set encounterquestion
     *
     * @param \General\GeneralBundle\Entity\EncounterQuestion $encounterquestion
     * @return Voting
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
     * Set answerforquestion
     *
     * @param \General\GeneralBundle\Entity\AnswerForQuestion $answerforquestion
     * @return Voting
     */
    public function setAnswerforquestion(\General\GeneralBundle\Entity\AnswerForQuestion $answerforquestion = null)
    {
        $this->answerforquestion = $answerforquestion;
    
        return $this;
    }

    /**
     * Get answerforquestion
     *
     * @return \General\GeneralBundle\Entity\AnswerForQuestion 
     */
    public function getAnswerforquestion()
    {
        return $this->answerforquestion;
    }

    /**
     * Set answerforencounterquestion
     *
     * @param \General\GeneralBundle\Entity\AnswerForEncounterQuestion $answerforencounterquestion
     * @return Voting
     */
    public function setAnswerforencounterquestion(\General\GeneralBundle\Entity\AnswerForEncounterQuestion $answerforencounterquestion = null)
    {
        $this->answerforencounterquestion = $answerforencounterquestion;
    
        return $this;
    }

    /**
     * Get answerforencounterquestion
     *
     * @return \General\GeneralBundle\Entity\AnswerForEncounterQuestion 
     */
    public function getAnswerforencounterquestion()
    {
        return $this->answerforencounterquestion;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->user = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
   
  
    /**
     * Set question
     *
     * @param \General\GeneralBundle\Entity\Question $question
     * @return Voting
     */
    public function setQuestion(\General\GeneralBundle\Entity\Question $question = null)
    {
        $this->question = $question;
    
        return $this;
    }

    /**
     * Get question
     *
     * @return \General\GeneralBundle\Entity\Question 
     */
    public function getQuestion()
    {
        return $this->question;
    }

    /**
     * Set seen
     *
     * @param boolean $seen
     * @return Voting
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
     * Set user_voting
     *
     * @param \Aristos\CoreBundle\Entity\User $userVoting
     * @return Voting
     */
    public function setUserVoting(\Aristos\CoreBundle\Entity\User $userVoting = null)
    {
        $this->user_voting = $userVoting;

        return $this;
    }

    /**
     * Get user_voting
     *
     * @return \Aristos\CoreBundle\Entity\User 
     */
    public function getUserVoting()
    {
        return $this->user_voting;
    }

    /**
     * Set user_receiving
     *
     * @param \Aristos\CoreBundle\Entity\User $userReceiving
     * @return Voting
     */
    public function setUserReceiving(\Aristos\CoreBundle\Entity\User $userReceiving = null)
    {
        $this->user_receiving = $userReceiving;

        return $this;
    }

    /**
     * Get user_receiving
     *
     * @return \Aristos\CoreBundle\Entity\User 
     */
    public function getUserReceiving()
    {
        return $this->user_receiving;
    }
}
