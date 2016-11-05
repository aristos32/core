<?php 
namespace Aristos\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use FOS\UserBundle\Model\User as BaseUser;
use General\GeneralBundle\Entity\Question as Question;
use Aristos\CoreBundle\Entity\Contacts as Contacts;
use Aristos\CoreBundle\Entity\ContactRequest as ContactRequest; 


/**
 * @ORM\Entity(repositoryClass="Aristos\CoreBundle\Entity\Repository\UserRepository")
 * @ORM\Table(name="user")
 * @UniqueEntity(fields="username", message="Email already taken")
 */
class User extends BaseUser implements AdvancedUserInterface, \Serializable 
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     * a username to show to the client, that can be same for many
     */
    protected $internalusername;
    
    
    
    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     */
    protected $gender = 'female';
    
        
    /**
     * @ORM\Column(type="string", length=50)
     * 127.0.0.1 or 231.23.123.98.34
     * ip user first created his profile
     */
    protected $ip = '127.0.0.1';   
    
         
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $firstName;
    
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $lastName;
    
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $city;
    
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $country;
    
    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $birthDate;
    
    /**
     * @ORM\Column(type="datetime")
     */
    protected $registerDate;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $profileViews = 0;
    
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $aboutMe;
    
    /**
     * @ORM\Column(type="boolean")
     */
    protected $deleted =  false;
    
    /**
     * @ORM\Column(type="boolean")
     */
    protected $termsAccepted = false;
    
    /**
     * not answering activation emails
     * 
     * @ORM\Column(type="boolean")
     */
    protected $notReachable = false;
    
    
    /**
     * @ORM\OneToMany(targetEntity="\General\GeneralBundle\Entity\Question", mappedBy="user")
     * questions user has asked
     */
    protected $questions;
    
    /**
     * @ORM\OneToMany(targetEntity="\General\GeneralBundle\Entity\EncounterQuestion", mappedBy="user")
     */
    protected $encounterquestions;
    
    /**
     * @ORM\OneToMany(targetEntity="\General\GeneralBundle\Entity\AnswerForQuestion", mappedBy="user")
     */
    protected $answersforquestions;
    
    /**
     * @ORM\OneToMany(targetEntity="\General\GeneralBundle\Entity\AnswerForEncounterQuestion", mappedBy="user")
     */
    protected $answersforencounterquestions;
    
    /**
     * @ORM\OneToMany(targetEntity="Contacts", mappedBy="user")
     */
    protected $currentContact;
    
    /**
     * @ORM\OneToMany(targetEntity="Contacts", mappedBy="user")
     */
    protected $otherContact;
    
    /**
     * @ORM\OneToMany(targetEntity="ContactRequest", mappedBy="user")
     */
    protected $senderContactRequest;
    
    /**
     * @ORM\OneToMany(targetEntity="ContactRequest", mappedBy="user")
     */
    protected $receiverContactRequest;
    
     /**
     * @ORM\oneToMany(targetEntity="\Aristos\CoreBundle\Entity\Voting", mappedBy="user")
     * votes casted by user, not votes on user questions/answers
     **/
    protected $votes;
    
    /**
     * @ORM\oneToMany(targetEntity="\Aristos\CoreBundle\Entity\Document", mappedBy="user")
     * documents uploaded by user
     **/
    protected $documents;
    
    /**
     * @ORM\oneToMany(targetEntity="\Aristos\CoreBundle\Entity\Hobby", mappedBy="user")
     * 
     * user hobbies
     **/
    protected $hobbies;
    
    /**
     * @ORM\oneToMany(targetEntity="\Aristos\CoreBundle\Entity\Note", mappedBy="user")
     *
     * user notes in various questions
     **/
    protected $notes;
    
    
    /**
     * @ORM\OneToOne(targetEntity="\General\GeneralBundle\Entity\Reputation", mappedBy="user")
     */
    protected $reputation;
    
    /**
     * 
     * @ORM\Column(type="array") 
     * additional user information - not main - specific for each project
     * 
     */
	protected  $userinfo = array();
	
	/**
	 * @ORM\Column(type="string")
	 * @Assert\NotBlank()
	 */
	protected $relationshipStatus = 'single';
	
	/**
	 * @ORM\Column(type="string")
	 * @Assert\NotBlank()
	 */
	protected $sexualOrientation = 'straight';
	
	/**
	 * @ORM\Column(type="string")
	 * 
	 */
	protected $interestedIn = 'men';
	
	/**
	 * see param profile.lookingfor
	 * @ORM\Column(type="string")
	 * 
	 */
	protected $lookingFor = 'n/a';
	
	/**
	 * @ORM\Column(type="string", nullable=true)
	 *
	 */
	protected $dreamTrip;
	    
    /**
     * @ORM\OneToMany(targetEntity="\General\GeneralBundle\Entity\EncounterRatingAnalytics", mappedBy="user")
     * has totals of all encounter ratings
     */
    protected $encounterratinganalytics;
    
    
    //set some default values
    public function __construct()
    {
    	parent::__construct();
    	
    	$this->setRegisterDate(new \DateTime()); 

    	$this->salt = md5(uniqid(null, true));
    	$this->relationshipStatus = 'single';
    	$this->sexualOrientation = 'straight';
    	$this->interestedIn = 'men';
 		$this->lookingFor = 'n/a'; 
    	$this->setIp($_SERVER['REMOTE_ADDR']);
    }
    
    public function __toString()
    {
    	return $this->getUsername();
    }
    

   /**
     * Sets the email.
     *
     * @param string $email
     * @return User
     */
    public function setEmail($email)
    {
    	//aaresti usename is the email
        $this->setUsername($email);

        return parent::setEmail($email);
    }

    /**
     * Set the canonical email.
     *
     * @param string $emailCanonical
     * @return User
     */
    public function setEmailCanonical($emailCanonical)
    {
    	//aaresti usename is the email
        $this->setUsernameCanonical($emailCanonical);
        $this->setUsername($emailCanonical);

        return parent::setEmailCanonical($emailCanonical);
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
     * Set firstName
     *
     * @param string $firstName
     * @return User
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    
        return $this;
    }

    /**
     * Get firstName
     *
     * @return string 
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     * @return User
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    
        return $this;
    }

    /**
     * Get lastName
     *
     * @return string 
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set country
     *
     * @param string $country
     * @return User
     */
    public function setCountry($country)
    {
        $this->country = $country;
    
        return $this;
    }

    /**
     * Get country
     *
     * @return string 
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set birthDate
     *
     * @param \DateTime $birthDate
     * @return User
     */
    public function setBirthDate($birthDate)
    {
        $this->birthDate = new \DateTime($birthDate);
    
        return $this;
    }

    /**
     * Get birthDate
     *
     * @return \DateTime 
     */
    public function getBirthDate()
    {
        return $this->birthDate;
    }

    /**
     * Set registerDate
     *
     * @param \DateTime $registerDate
     * @return User
     */
    public function setRegisterDate($registerDate)
    {
        $this->registerDate = $registerDate;
    
        return $this;
    }

    /**
     * Get registerDate
     *
     * @return \DateTime 
     */
    public function getRegisterDate()
    {
        return $this->registerDate;
    }

   
    /**
     * Set profileViews
     *
     * @param integer $profileViews
     * @return User
     */
    public function setProfileViews($profileViews)
    {
        $this->profileViews = $profileViews;
    
        return $this;
    }

    /**
     * Get profileViews
     *
     * @return integer 
     */
    public function getProfileViews()
    {
        return $this->profileViews;
    }

    /**
     * Set aboutMe
     *
     * @param string $aboutMe
     * @return User
     */
    public function setAboutMe($aboutMe)
    {
        $this->aboutMe = $aboutMe;
    
        return $this;
    }

    /**
     * Get aboutMe
     *
     * @return string 
     */
    public function getAboutMe()
    {
        return $this->aboutMe;
    }

   

    /**
     * @inheritDoc
     */
    public function eraseCredentials()
    {
    }

    /**
     * @see \Serializable::serialize()
     */
    public function serialize()
    {
    	return serialize(array(
    			$this->id,
    	));
    }
    
    /**
     * @see \Serializable::unserialize()
     */
    public function unserialize($serialized)
    {
    	list (
    			$this->id,
    	) = unserialize($serialized);
    }
    
  
    /**
     * Set relationshipStatus
     *
     * @param string $relationshipStatus
     * @return User
     */
    public function setRelationshipStatus($relationshipStatus)
    {
        $this->relationshipStatus = $relationshipStatus;
    
        return $this;
    }

    /**
     * Get relationshipStatus
     *
     * @return string 
     */
    public function getRelationshipStatus()
    {
        return $this->relationshipStatus;
    }

    /**
     * Set sexualOrientation
     *
     * @param string $sexualOrientation
     * @return User
     */
    public function setSexualOrientation($sexualOrientation)
    {
        $this->sexualOrientation = $sexualOrientation;
    
        return $this;
    }

    /**
     * Get sexualOrientation
     *
     * @return string 
     */
    public function getSexualOrientation()
    {
        return $this->sexualOrientation;
    }

    /**
     * Set deleted
     *
     * @param string $deleted
     * @return User
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;
    
        return $this;
    }

    /**
     * Get deleted
     *
     * @return string 
     */
    public function getDeleted()
    {
        return $this->deleted;
    }

  

    /**
     * Add questions
     *
     * @param \General\GeneralBundle\Entity\Question $questions
     * @return User
     */
    public function addQuestion(\General\GeneralBundle\Entity\Question $questions)
    {
        $this->questions[] = $questions;
    
        return $this;
    }

    /**
     * Remove questions
     *
     * @param \General\GeneralBundle\Entity\Question $questions
     */
    public function removeQuestion(\General\GeneralBundle\Entity\Question $questions)
    {
        $this->questions->removeElement($questions);
    }

    /**
     * Get questions
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getQuestions()
    {
        return $this->questions;
    }

   
    /**
     * Set ip
     *
     * @param string $ip
     * @return User
     */
    public function setIp($ip)
    {
        $this->ip = $ip;
    
        return $this;
    }

    /**
     * Get ip
     *
     * @return string 
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * Add encounterquestions
     *
     * @param \General\GeneralBundle\Entity\EncounterQuestion $encounterquestions
     * @return User
     */
    public function addEncounterquestion(\General\GeneralBundle\Entity\EncounterQuestion $encounterquestions)
    {
        $this->encounterquestions[] = $encounterquestions;
    
        return $this;
    }

    /**
     * Remove encounterquestions
     *
     * @param \General\GeneralBundle\Entity\EncounterQuestion $encounterquestions
     */
    public function removeEncounterquestion(\General\GeneralBundle\Entity\EncounterQuestion $encounterquestions)
    {
        $this->encounterquestions->removeElement($encounterquestions);
    }

    /**
     * Get encounterquestions
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getEncounterquestions()
    {
        return $this->encounterquestions;
    }

    
    
    /**
     * Add answersforquestions
     *
     * @param \General\GeneralBundle\Entity\AnswerForQuestion $answersforquestions
     * @return User
     */
    public function addAnswersforquestion(\General\GeneralBundle\Entity\AnswerForQuestion $answersforquestions)
    {
        $this->answersforquestions[] = $answersforquestions;
    
        return $this;
    }

    /**
     * Remove answersforquestions
     *
     * @param \General\GeneralBundle\Entity\AnswerForQuestion $answersforquestions
     */
    public function removeAnswersforquestion(\General\GeneralBundle\Entity\AnswerForQuestion $answersforquestions)
    {
        $this->answersforquestions->removeElement($answersforquestions);
    }

    /**
     * Get answersforquestions
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAnswersforquestions()
    {
        return $this->answersforquestions;
    }

    /**
     * Add answersforencounterquestions
     *
     * @param \General\GeneralBundle\Entity\AnswerForEncounterQuestion $answersforencounterquestions
     * @return User
     */
    public function addAnswersforencounterquestion(\General\GeneralBundle\Entity\AnswerForEncounterQuestion $answersforencounterquestions)
    {
        $this->answersforencounterquestions[] = $answersforencounterquestions;
    
        return $this;
    }

    /**
     * Remove answersforencounterquestions
     *
     * @param \General\GeneralBundle\Entity\AnswerForEncounterQuestion $answersforencounterquestions
     */
    public function removeAnswersforencounterquestion(\General\GeneralBundle\Entity\AnswerForEncounterQuestion $answersforencounterquestions)
    {
        $this->answersforencounterquestions->removeElement($answersforencounterquestions);
    }

    /**
     * Get answersforencounterquestions
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAnswersforencounterquestions()
    {
        return $this->answersforencounterquestions;
    }

    /**
     * Set termsAccepted
     *
     * @param boolean $termsAccepted
     * @return User
     */
    public function setTermsAccepted($termsAccepted)
    {
        $this->termsAccepted = $termsAccepted;
    
        return $this;
    }

    /**
     * Get termsAccepted
     *
     * @return boolean 
     */
    public function getTermsAccepted()
    {
        return $this->termsAccepted;
    }

    

   

    /**
     * Get votes
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getVotes()
    {
        return $this->votes;
    }

    /**
     * Set reputation
     *
     * @param \General\GeneralBundle\Entity\Reputation $reputation
     * @return User
     */
    public function setReputation(\General\GeneralBundle\Entity\Reputation $reputation = null)
    {
        $this->reputation = $reputation;
    
        return $this;
    }

    /**
     * Get reputation
     *
     * @return \General\GeneralBundle\Entity\Reputation 
     */
    public function getReputation()
    {
        return $this->reputation;
    }

    /**
     * Set encounterratinganalytics
     *
     * @param \General\GeneralBundle\Entity\EncounterRatingAnalytics $encounterratinganalytics
     * @return User
     */
    public function setEncounterratinganalytics(\General\GeneralBundle\Entity\EncounterRatingAnalytics $encounterratinganalytics = null)
    {
        $this->encounterratinganalytics = $encounterratinganalytics;
    
        return $this;
    }

    /**
     * Get encounterratinganalytics
     *
     * @return \General\GeneralBundle\Entity\EncounterRatingAnalytics 
     */
    public function getEncounterratinganalytics()
    {
        return $this->encounterratinganalytics;
    }

    /**
     * Set internalusername
     *
     * @param string $internalusername
     * @return User
     */
    public function setInternalusername($internalusername)
    {
        $this->internalusername = $internalusername;

        return $this;
    }

    /**
     * Get internalusername
     *
     * @return string 
     */
    public function getInternalusername()
    {
        return $this->internalusername;
    }

    /**
     * Set userinfo
     *
     * @param array $userinfo
     * @return User
     */
    public function setUserinfo($userinfo)
    {
        $this->userinfo = $userinfo;

        return $this;
    }

    /**
     * Get userinfo
     *
     * @return array 
     */
    public function getUserinfo()
    {
        return $this->userinfo;
    }

    /**
     * Set city
     *
     * @param string $city
     * @return User
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return string 
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set interestedIn
     *
     * @param string $interestedIn
     * @return User
     */
    public function setInterestedIn($interestedIn)
    {
        $this->interestedIn = $interestedIn;

        return $this;
    }

    /**
     * Get interestedIn
     *
     * @return string 
     */
    public function getInterestedIn()
    {
        return $this->interestedIn;
    }

    /**
     * Set lookingFor
     *
     * @param string $lookingFor
     * @return User
     */
    public function setLookingFor($lookingFor)
    {
        $this->lookingFor = $lookingFor;

        return $this;
    }

    /**
     * Get lookingFor
     *
     * @return string 
     */
    public function getLookingFor()
    {
        return $this->lookingFor;
    }

    /**
     * Add votes
     *
     * @param \Aristos\CoreBundle\Entity\Voting $votes
     * @return User
     */
    public function addVote(\Aristos\CoreBundle\Entity\Voting $votes)
    {
        $this->votes[] = $votes;

        return $this;
    }

    /**
     * Remove votes
     *
     * @param \Aristos\CoreBundle\Entity\Voting $votes
     */
    public function removeVote(\Aristos\CoreBundle\Entity\Voting $votes)
    {
        $this->votes->removeElement($votes);
    }

    /**
     * Set dreamTrip
     *
     * @param string $dreamTrip
     * @return User
     */
    public function setDreamTrip($dreamTrip)
    {
        $this->dreamTrip = $dreamTrip;

        return $this;
    }

    /**
     * Get dreamTrip
     *
     * @return string 
     */
    public function getDreamTrip()
    {
        return $this->dreamTrip;
    }

    /**
     * Add documents
     *
     * @param \Aristos\CoreBundle\Entity\Document $documents
     * @return User
     */
    public function addDocument(\Aristos\CoreBundle\Entity\Document $documents)
    {
        $this->documents[] = $documents;

        return $this;
    }

    /**
     * Remove documents
     *
     * @param \Aristos\CoreBundle\Entity\Document $documents
     */
    public function removeDocument(\Aristos\CoreBundle\Entity\Document $documents)
    {
        $this->documents->removeElement($documents);
    }

    /**
     * Get documents
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getDocuments()
    {
        return $this->documents;
    }

    /**
     * Add currentContact
     *
     * @param \Aristos\CoreBundle\Entity\Contacts $currentContact
     * @return User
     */
    public function addCurrentContact(\Aristos\CoreBundle\Entity\Contacts $currentContact)
    {
        $this->currentContact[] = $currentContact;

        return $this;
    }

    /**
     * Remove currentContact
     *
     * @param \Aristos\CoreBundle\Entity\Contacts $currentContact
     */
    public function removeCurrentContact(\Aristos\CoreBundle\Entity\Contacts $currentContact)
    {
        $this->currentContact->removeElement($currentContact);
    }

    /**
     * Get currentContact
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCurrentContact()
    {
        return $this->currentContact;
    }

    /**
     * Add otherContact
     *
     * @param \Aristos\CoreBundle\Entity\Contacts $otherContact
     * @return User
     */
    public function addOtherContact(\Aristos\CoreBundle\Entity\Contacts $otherContact)
    {
        $this->otherContact[] = $otherContact;

        return $this;
    }

    /**
     * Remove otherContact
     *
     * @param \Aristos\CoreBundle\Entity\Contacts $otherContact
     */
    public function removeOtherContact(\Aristos\CoreBundle\Entity\Contacts $otherContact)
    {
        $this->otherContact->removeElement($otherContact);
    }

    /**
     * Get otherContact
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getOtherContact()
    {
        return $this->otherContact;
    }

    /**
     * Add senderContactRequest
     *
     * @param \Aristos\CoreBundle\Entity\ContactRequest $senderContactRequest
     * @return User
     */
    public function addSenderContactRequest(\Aristos\CoreBundle\Entity\ContactRequest $senderContactRequest)
    {
        $this->senderContactRequest[] = $senderContactRequest;

        return $this;
    }

    /**
     * Remove senderContactRequest
     *
     * @param \Aristos\CoreBundle\Entity\ContactRequest $senderContactRequest
     */
    public function removeSenderContactRequest(\Aristos\CoreBundle\Entity\ContactRequest $senderContactRequest)
    {
        $this->senderContactRequest->removeElement($senderContactRequest);
    }

    /**
     * Get senderContactRequest
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSenderContactRequest()
    {
        return $this->senderContactRequest;
    }

    /**
     * Add receiverContactRequest
     *
     * @param \Aristos\CoreBundle\Entity\ContactRequest $receiverContactRequest
     * @return User
     */
    public function addReceiverContactRequest(\Aristos\CoreBundle\Entity\ContactRequest $receiverContactRequest)
    {
        $this->receiverContactRequest[] = $receiverContactRequest;

        return $this;
    }

    /**
     * Remove receiverContactRequest
     *
     * @param \Aristos\CoreBundle\Entity\ContactRequest $receiverContactRequest
     */
    public function removeReceiverContactRequest(\Aristos\CoreBundle\Entity\ContactRequest $receiverContactRequest)
    {
        $this->receiverContactRequest->removeElement($receiverContactRequest);
    }

    /**
     * Get receiverContactRequest
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getReceiverContactRequest()
    {
        return $this->receiverContactRequest;
    }

    /**
     * Add encounterratinganalytics
     *
     * @param \General\GeneralBundle\Entity\EncounterRatingAnalytics $encounterratinganalytics
     * @return User
     */
    public function addEncounterratinganalytic(\General\GeneralBundle\Entity\EncounterRatingAnalytics $encounterratinganalytics)
    {
        $this->encounterratinganalytics[] = $encounterratinganalytics;

        return $this;
    }

    /**
     * Remove encounterratinganalytics
     *
     * @param \General\GeneralBundle\Entity\EncounterRatingAnalytics $encounterratinganalytics
     */
    public function removeEncounterratinganalytic(\General\GeneralBundle\Entity\EncounterRatingAnalytics $encounterratinganalytics)
    {
        $this->encounterratinganalytics->removeElement($encounterratinganalytics);
    }

    /**
     * Add hobbies
     *
     * @param \Aristos\CoreBundle\Entity\Hobby $hobbies
     * @return User
     */
    public function addHobby(\Aristos\CoreBundle\Entity\Hobby $hobbies)
    {
        $this->hobbies[] = $hobbies;

        return $this;
    }

    /**
     * Remove hobbies
     *
     * @param \Aristos\CoreBundle\Entity\Hobby $hobbies
     */
    public function removeHobby(\Aristos\CoreBundle\Entity\Hobby $hobbies)
    {
        $this->hobbies->removeElement($hobbies);
    }

    /**
     * Get hobbies
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getHobbies()
    {
        return $this->hobbies;
    }


    /**
     * Add notes
     *
     * @param \Aristos\CoreBundle\Entity\Note $notes
     * @return User
     */
    public function addNote(\Aristos\CoreBundle\Entity\Note $notes)
    {
        $this->notes[] = $notes;

        return $this;
    }

    /**
     * Remove notes
     *
     * @param \Aristos\CoreBundle\Entity\Note $notes
     */
    public function removeNote(\Aristos\CoreBundle\Entity\Note $notes)
    {
        $this->notes->removeElement($notes);
    }

    /**
     * Get notes
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * Set gender
     *
     * @param string $gender
     * @return User
     */
    public function setGender($gender)
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * Get gender
     *
     * @return string 
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * Set notReachable
     *
     * @param boolean $notReachable
     * @return User
     */
    public function setNotReachable($notReachable)
    {
        $this->notReachable = $notReachable;

        return $this;
    }

    /**
     * Get notReachable
     *
     * @return boolean 
     */
    public function getNotReachable()
    {
        return $this->notReachable;
    }
}
