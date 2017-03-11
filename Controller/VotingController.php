<?php
namespace Aristos\CoreBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Aristos\CoreBundle\Entity\Voting;
use General\GeneralBundle\Form\Type\EncounterRatingType;
use General\GeneralBundle\Entity\EncounterRating;
use General\GeneralBundle\Entity\Reputation;
use General\GeneralBundle\Controller\MyBaseController;
use General\GeneralBundle\Entity\EncounterRatingAnalytics;
/**
 * controller.
 */
class VotingController extends MyBaseController
{
	
	/**
	 * add/remove reputation using AJAX
	 * 
	 * for questions or encounterquestionss
	 * 
	 * type = question or encounterquestion
	 */
	public function reputationAction(Request $request) {
			
		$errormessage = "";
		
		$em = $this->getDoctrine ()->getManager ();
		
		$questionid = $request->request->get ( 'questionid' );
		$votes = $request->request->get ( 'votes' );
		$type = $request->request->get ( 'type' );

		
		// check if current user already voted for this
		$user = $this->get ( 'security.context' )->getToken ()->getUser ();
		
		//echo $user->getUsername().", $questionid $type"; exit;
		
		//user cannot vote for his own question
		//$ownQuestion = $em->getRepository('GeneralGeneralBundle:Question')->checkUserOwnQuestion($user->getId(), $questionid);
		
			
		//echo $questionid;exit;
		
		if($type=='question')
		{
			$question = $em->getRepository('GeneralGeneralBundle:Question')->findOneBy(array('id' => $questionid));	
			//$ownQuestion = $em->getRepository('GeneralGeneralBundle:Question')->findOneBy(array('user' => $user, 'id' => $questionid));
			//echo $user->getId();exit;
			//$voting = $em->getRepository('GeneralGeneralBundle:Voting')->getVoting($questionid, $user->getId());
			$voting = $em->getRepository('AristosCoreBundle:Voting')->findOneBy(array('user_voting' => $user, 'question' => $question));
		}
		else if($type=='encounterquestion')
		{
			$question = $em->getRepository('GeneralGeneralBundle:EncounterQuestion')->findOneBy(array('id' => $questionid));	
			//$ownQuestion = $em->getRepository('GeneralGeneralBundle:EncounterQuestion')->findOneBy(array('user' => $user, 'id' => $questionid));
			//$voting = $em->getRepository('GeneralGeneralBundle:Voting')->getEncounterQuestionVoting($questionid, $user->getId());
			$voting = $em->getRepository('AristosCoreBundle:Voting')->findOneBy(array('user_voting' => $user, 'encounterquestion' => $question));
		}
		
		//other user that asked the question
		$questionOwner = $question->getUser();
		
		//if(!empty($ownQuestion))
		if($user==$questionOwner)
		{
			$errormessage = "Cannot Vote your own question";
			$return=array("responseCode"=>400, "totalVotes"=>'', "errormessage"=>$errormessage);
			$return=json_encode($return);//jscon encode the array
			return new Response($return,200,array('Content-Type'=>'application/json'));
			
		}
		
		//var_dump($voting); exit;
		
		if(empty($voting))
		{
			//\Doctrine\Common\Util\Debug::dump($voting);exit;
			//add one vote
			$vote = new Voting();
			if($type=='question')
			{
				$vote->setQuestion($question);
			}
			else if($type=='encounterquestion')
			{
				$vote->setEncounterquestion($question);
			}
			
			$question->setTotalvotes($question->getTotalvotes()+$votes);
			$em->merge($question);
			
			$vote->setUserVoting($user);
			$vote->setUserReceiving($questionOwner);	
			$vote->setVotes($votes);
			$em->persist($vote);
			$em->flush();
				
			//current user votes casted
			$reputation = $user->getReputation();
			$reputation->setUser ( $user );
			if ($votes == 1)
				$reputation->setUpVotesCasted ( $reputation->getUpVotesCasted () + $votes );
			else
				$reputation->setDownVotesCasted ( $reputation->getDownVotesCasted () + $votes );
			$em->merge( $reputation );
			$em->flush ();
			
			$reputation = $em->getRepository('GeneralGeneralBundle:Reputation')->findOneBy(array('user' => $questionOwner));
			$reputation->setUser($questionOwner);
			if($type=='question')
			{
				$reputation->setVotesReceivedOnQuestions($reputation->getVotesReceivedOnQuestions()+
					$votes);
			}
			else if($type=='encounterquestion')
			{
				$reputation->setVotesReceivedOnEncounterQuestions($reputation->getVotesReceivedOnEncounterQuestions()+
					$votes);
			}	
	
			$reputation->setTotalReputation(
					$reputation->getSystemPoints() +
					($reputation->getVotesReceivedOnQuestions()+$reputation->getVotesReceivedOnEncounterQuestions())*$this->container->getParameter('reputation.pointsperquestion') +
					($reputation->getVotesReceivedOnAnswers()+$reputation->getVotesReceivedOnAnswersForEncounterQuestions())*$this->container->getParameter('reputation.pointsperanswer') +
					$reputation->getAcceptedAnswers() * $this->container->getParameter('reputation.pointsperacceptedanswer'));
			$em->merge($reputation);
			$em->flush();
			
			//echo $questionOwnerId;exit;
			
			
			
			//echo "Created Product with ID " . $vote->getId() . "\n";exit;
			
			
		
		}
		else {
			$errormessage = "Already Voted";
			$return=array("responseCode"=>400, "totalVotes"=>'', "errormessage"=>$errormessage);
			$return=json_encode($return);//jscon encode the array
			return new Response($return,200,array('Content-Type'=>'application/json'));
		}
		
		if($type=='question')
		{
			//change reputation in question table
			
			//aarresti cheeck if needed back
			//$update = $em->getRepository('GeneralGeneralBundle:Question')->updateQuestionVotes($votes, $questionid);
			
			//return new reputation
			//$question = $em->getRepository('GeneralGeneralBundle:Question')->getQuestion($questionid);
			$question = $em->getRepository('GeneralGeneralBundle:Question')->findOneBy(array('id' => $questionid));
		}
		else if($type=='encounterquestion')
		{
			//change reputation in question table
			//$update = $em->getRepository('GeneralGeneralBundle:EncounterQuestion')->updateVotes($votes, $questionid);
				
			//return new reputation
			//$question = $em->getRepository('GeneralGeneralBundle:EncounterQuestion')->getEncounterQuestion($questionid);
			$question = $em->getRepository('GeneralGeneralBundle:EncounterQuestion')->findOneBy(array('id' => $questionid));
		
		}
		
		
		
		$votes = $question->getVotesCount();		
		//\Doctrine\Common\Util\Debug::dump($votes);exit;
		
		//$totalVotes = $newVotes;
		$return=array("responseCode"=>200,  "totalVotes"=>$votes, "errormessage"=>$errormessage);
				
		$return=json_encode($return);//jscon encode the array
		return new Response($return,200,array('Content-Type'=>'application/json'));//make sure it has the correct content type
	}
	

	/**
	 *	
	 *  add/remove an entry in the Voting table
	 *
	 *  voting for an answer, using ajax. For answers for both question and encounterquestion
	 *
	 */
	public function answerReputationAction(Request $request)
	{
		 
		$errormessage = "";
	
		$em = $this->getDoctrine()->getManager();
		 
		$totalvotes = 0;
		//$errormessage = "Need to login to vote";
		//$return=array("responseCode"=>400,  "errormessage"=>$errormessage);
	
		$answerid = $request->request->get('answerid');
		$votes = $request->request->get('votes');
		$type = $request->request->get('type');
		 
		if( $this->container->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY') == false ){
			$errormessage = "Login to vote";
			$return=array("responseCode"=>400, "totalVotes"=>'', "errormessage"=>$errormessage, 'answerid'=>$answerid);
			$return=json_encode($return);//jscon encode the array
			return new Response($return,200,array('Content-Type'=>'application/json'));
		}
		 
		//check if current user already voted for this
		$user = $this->get('security.context')->getToken()->getUser();
	
		//echo $type.' '.$answerid;
		 
		if($type=='question')
		{
			$answer = $em->getRepository('GeneralGeneralBundle:AnswerForQuestion')->findOneBy(array('id' => $answerid));
			//$ownAnswer = $em->getRepository('GeneralGeneralBundle:AnswerForQuestion')->findOneBy(array('user' => $user, 'id' => $answerid));
			$answerUser = $answer->getUser();
			$voting = $em->getRepository('AristosCoreBundle:Voting')->findOneBy(array('user_voting' => $user, 'answerforquestion' => $answer));
		}
		else if($type=='encounterquestion')
		{
			$answer = $em->getRepository('GeneralGeneralBundle:AnswerForEncounterQuestion')->findOneBy(array('id' => $answerid));
			//$ownAnswer = $em->getRepository('GeneralGeneralBundle:AnswerForEncounterQuestion')->findOneBy(array('user' => $user, 'id' => $answerid));
			$answerUser = $answer->getUser();
			$voting = $em->getRepository('AristosCoreBundle:Voting')->findOneBy(array('user_voting' => $user, 'answerforencounterquestion' => $answer));
		}
		else
		{
			$errormessage = "Fatal Error";
			$return=array("responseCode"=>400, "totalVotes"=>'', "answerid"=>$answerid, "errormessage"=>$errormessage);
			$return=json_encode( $return ); // jscon encode the array
			return new Response ( $return, 200, array (
					'Content-Type' => 'application/json'
			) );
		}
		//exit;
		 
		//if(!empty($ownAnswer))
		if($user==$answerUser)
		{
			$errormessage = "Cannot Vote your own answer";
			$return=array("responseCode"=>400, "totalVotes"=>'', "answerid"=>$answerid, "errormessage"=>$errormessage);
			$return=json_encode( $return ); // jscon encode the array
			return new Response ( $return, 200, array (
					'Content-Type' => 'application/json'
			) );
		}
	
	
	
		if (empty ( $voting )) {
			// \Doctrine\Common\Util\Debug::dump($voting);exit;
			// add one vote
				
				
			$vote = new Voting ();
				
			if ($type == 'question')
				$vote->setAnswerforquestion ( $answer );
			else
				$vote->setAnswerforencounterquestion ( $answer);
				
			$vote->setUserVoting($user);
			$vote->setUserReceiving($answer->getUser());
			$vote->setVotes($votes);
			$em->persist($vote);
			$em->flush();
	
			$answer->setTotalvotes($answer->getTotalvotes()+$votes);
			$em->merge($answer);
	
			//current user votes casted
			$reputation = $user->getReputation();
			//first access - create registration entries
			//@todo is this duplicate of AuthenticationSuccessListener::initReputation
			if(empty($reputation))
			{
				//user received some initial reputation
				$vote = new Voting();
				$vote->setUserReceiving($user);
				$vote->setPoints($this->container->getParameter('reputation.registrationpoints'));
				$vote->setType("initial register");
				$em->persist($vote);
			
				//update reputation summary table
				$reputation = new Reputation();
				$reputation->setUser($user);
				$reputation->setSystemPoints($this->container->getParameter('reputation.registrationpoints'));
				$reputation->setTotalReputation($this->container->getParameter('reputation.registrationpoints'));
				$em->persist($reputation);
							
				$em->flush();
			
				//reload user with new objects
				$user = $em->getRepository('AristosCoreBundle:User')->findOneBy(array('id' => $user_id));
			}
			
			if ($votes == 1)
				$reputation->setUpVotesCasted ( $reputation->getUpVotesCasted () + $votes );
			else
				$reputation->setDownVotesCasted ( $reputation->getDownVotesCasted () + $votes );
			$em->merge( $reputation );
			$em->flush ();
	
			//other user that answered
			$answerOwner = $answer->getUser();
			$reputation = $em->getRepository('GeneralGeneralBundle:Reputation')->findOneBy(array('user' => $answerOwner));
			$reputation->setUser($answerOwner);
			if($type=='question')
			{
				$reputation->setVotesReceivedOnAnswers($reputation->getVotesReceivedOnAnswers()+
						$votes);
			}
			else if($type=='encounterquestion')
			{
				$reputation->setVotesReceivedOnAnswersForEncounterQuestions($reputation->getVotesReceivedOnAnswersForEncounterQuestions()+
						$votes);
			}
	
			$reputation->setTotalReputation(
					$reputation->getSystemPoints() +
					($reputation->getVotesReceivedOnQuestions()+$reputation->getVotesReceivedOnEncounterQuestions())*$this->container->getParameter('reputation.pointsperquestion') +
					($reputation->getVotesReceivedOnAnswers()+$reputation->getVotesReceivedOnAnswersForEncounterQuestions())*$this->container->getParameter('reputation.pointsperanswer') +
					$reputation->getAcceptedAnswers() * $this->container->getParameter('reputation.pointsperacceptedanswer'));
			$em->merge($reputation);
			$em->flush();
		}
		else {
			//$totalvotes = $answer->getVotesCount();
			//echo $votes;exit;
	
			$errormessage = "Already Voted";
			$return=array("responseCode"=>400, "totalVotes"=>'', "answerid"=>$answerid, "errormessage"=>$errormessage);
			$return=json_encode($return);//jscon encode the array
			return new Response($return,200,array('Content-Type'=>'application/json'));
		}
	
	
		//return new reputation
		if($type=='question')
			$answer = $em->getRepository('GeneralGeneralBundle:AnswerForQuestion')->findOneBy(array('id' => $answerid));
		else
			$answer = $em->getRepository('GeneralGeneralBundle:AnswerForEncounterQuestion')->findOneBy(array('id' => $answerid));
	
		$totalvotes = $answer->getTotalvotes();
		 
			
		//$votes = $answer->getTotalvotes();
	
		$return=array("responseCode"=>200,  "totalVotes"=>$totalvotes, "answerid"=>$answerid, "errormessage"=>$errormessage);
	
		$return=json_encode($return); // jscon encode the array
		return new Response ( $return, 200, array (
				'Content-Type' => 'application/json'
		) ); // make sure it has the correct content type
	}
	
	/**
	 * 
	 * Rate an ACTUAL encounter(from both user sides)
	 * 
	 * @param id : questionid
	 * @param type : which user is doing the rating, {requester, answerer}
	 * 
	 */
	public function rateEncounterAction($id)
	{
		//echo __METHOD__;exit;
			
		$errormessage = "";
		//store data of current rating form
		$currentRating = null;
		$totalPointsReceived = 0;
		
		$em = $this->getDoctrine ()->getManager();

		$request = $this->getRequest ();
		
		//read $_POST
		$type = $request->request->get('type');		
		
				
		$encounterQuestion = $em->getRepository('GeneralGeneralBundle:EncounterQuestion')->findOneBy(array('id' => $id));
		
		//get EncounterRating entity
		$encounterRating = $em->getRepository('GeneralGeneralBundle:EncounterRating')->findOneBy(array('encounterQuestion' => $encounterQuestion));
		
		//requester 
		$requesteruser = $encounterQuestion->getUser();
		$requesteruserid = $requesteruser->getId();
		
		//get user with accepted answer
		$accepterUser = $encounterQuestion->getAccepterUser();

		$encounterRatingAnalytics = $this->container->getParameter('encounterratinganalytics.properties');
		$encounterRatingChoices = $this->container->getParameter('encounterratinganalytics.choices');
		
		$form = $this->createForm ( new EncounterRatingType ($encounterRatingAnalytics, $encounterRatingChoices), $currentRating );
		$form->bind ( $request );
		
		if ($form->isValid ()) {
			// Persist the entity - get rate attributes
			$currentRating = $form->getData ();
			//print_r($currentRating->getRequesterAttributes()); exit;
			$average = round( array_sum($currentRating->getRequesterAttributes()) / count($currentRating->getRequesterAttributes()), 1);
						
			//requester is rating now
			if($type == 'requester') {
				$encounterRating->setRequesterAverage ( $average );
				$encounterRating->setRequesterTestimonialDate(new \DateTime());
				$encounterRating->setRequesterAttributes ( $currentRating->getRequesterAttributes () );
				$encounterRating->setRequesterTestimonial( $currentRating->getRequesterTestimonial());
				
				//answerer also filled - set to public
				$attrs = $encounterRating->getAnswererAttributes();
				if(!empty($attrs))
				//if(!empty($encounterRating->getAnswererAttributes()))
				{
					$encounterRating->setPublic(true);
					$encounterRating->setAnswerercanview(true);
					$encounterRating->setRequestercanview(true);
				}
				else 
				{
					$now = new \DateTime();
					$daydiff = $now->diff($encounterRating->getEncounterQuestion()->getTodate())->format("%a");
					
					//after 15 days of the question todate, set visible for everyone
					if($daydiff > 15)
					{
						$encounterRating->setPublic(true);
						$encounterRating->setAnswerercanview(true);
						$encounterRating->setRequestercanview(true);
						
					}
					
					//echo $daydiff;exit;					
				}
				
				//exit;
				
								
				//print_r($currentRating->getRequesterAttributes ());exit;
				
				//requester votes for answerer
				foreach($currentRating->getRequesterAttributes () as $name => $points )
				{
					//sum for reputation table
					$totalPointsReceived = $totalPointsReceived + $points;
					
					//requester ratings added to accepter user totals
					$analyticsProperty = $em->getRepository('GeneralGeneralBundle:EncounterRatingAnalytics')->findOneBy(array('user' => $accepterUser, 'name' => $name));
					
					//print_r($accepterUser->getId()); echo $name; exit;
					
					if(empty($analyticsProperty))
					{
						$analyticsProperty = new EncounterRatingAnalytics();
						$analyticsProperty->setUser($accepterUser);
						$analyticsProperty->setName($name);
						$analyticsProperty->setTimesVoted(1);
						$analyticsProperty->setTotalPoints($points);
					}
					else 
					{
						$analyticsProperty->setTimesVoted( $analyticsProperty->getTimesVoted() + 1 );
						$analyticsProperty->setTotalPoints( $analyticsProperty->getTotalPoints() + $points);
						
					}
					
					$em->persist($analyticsProperty);
					
				}
				//echo $totalPointsReceived;exit;
				
				$reputation = $accepterUser->getReputation ();
				$reputation->setRatingPoints ( $reputation->getRatingPoints () + $totalPointsReceived );
				$reputation->setTotalReputation ( $reputation->getTotalReputation () + $totalPointsReceived );
				
				$em->persist ( $reputation );
				$em->persist ( $encounterRating );
				//exit;
				
			} 
			//answerer is rating now
			else
			{
				$encounterRating->setAnswererAverage ( $average );
				$encounterRating->setAnswererTestimonialDate(new \DateTime());
				$encounterRating->setAnswererAttributes ( $currentRating->getRequesterAttributes () );
				$encounterRating->setAnswererTestimonial( $currentRating->getRequesterTestimonial());
				
				//requester also filled - set to public
				$attrs = $encounterRating->getRequesterAttributes();
                if(!empty($attrs))
                //if(!empty($encounterRating->getRequesterAttributes()))
				{
					$encounterRating->setPublic(true);
					$encounterRating->setAnswerercanview(true);
					$encounterRating->setRequestercanview(true);
				}
				else
				{
					$now = new \DateTime();
					$daydiff = $now->diff($encounterRating->getEncounterQuestion()->getTodate())->format("%a");
					//after 15 days of the question todate, set visible for everyone
					if($daydiff > 15)
					{
						$encounterRating->setPublic(true);
						$encounterRating->setAnswerercanview(true);
						$encounterRating->setRequestercanview(true);
				
					}
					//echo $daydiff;exit;
				}
				
				//requester ratings added to accepter user totals
							
				//answerer votes for requester
				foreach($currentRating->getRequesterAttributes () as $name => $points )
				{
					//sum for reputation table
					$totalPointsReceived = $totalPointsReceived + $points;
						
					//requester ratings added to accepter user totals
					$analyticsProperty = $em->getRepository('GeneralGeneralBundle:EncounterRatingAnalytics')->findOneBy(array('user' => $requesteruser, 'name' => $name));
						
					if(empty($analyticsProperty))
					{
						$analyticsProperty = new EncounterRatingAnalytics();
						$analyticsProperty->setUser($accepterUser);
						$analyticsProperty->setName($name);
						$analyticsProperty->setTimesVoted(1);
						$analyticsProperty->setTotalPoints($points);
					}
					else
					{
						$analyticsProperty->setTimesVoted( $analyticsProperty->getTimesVoted() + 1 );
						$analyticsProperty->setTotalPoints( $analyticsProperty->getTotalPoints() + $points);				
					}
						
					$em->persist($analyticsProperty);
						
				}
				
				//echo $totalPointsReceived;exit;
				
				$reputation = $requesteruser->getReputation ();
				$reputation->setRatingPoints ( $reputation->getRatingPoints () + $totalPointsReceived );
				$reputation->setTotalReputation ( $reputation->getTotalReputation () + $totalPointsReceived );

				$em->persist ( $reputation );
				$em->persist ( $encounterRating );
			}
				
			//var_dump($currentRating->getRequesterAttributes ());
			
			
				
			//\Doctrine\Common\Util\Debug::dump($encounterRating->getAttributes());exit;
			$em->persist ( $encounterRating );
			$em->flush ();
		}
		
	
		return $this->redirect($this->generateUrl('GeneralGeneralBundle_account_questions', array(
				'user_id'=> $requesteruserid))
		);
		
	}
	
	/**
	 * 
	 * show the form for rate an 
	 * @param id : questionid
	 * @param type : type of user rating, {requester, answerer}
	 * 
	 */
	public function newRateEncounterAction(Request $request, $id)
	{
		//echo __METHOD__;exit;

		$errormessage = "";
	
		$em = $this->getDoctrine ()->getManager();
		
		// $_GET parameters from href
    	$type = $request->query->get('type');
    	//$id2 = $request->query->get('id');
    	
    	//echo $id2;exit;
	
		// check if current user already voted for this
		$user = $this->get ( 'security.context' )->getToken ()->getUser ();
		
		//get pending notifications
		$notifications = $this->getNotifications($user);
	
		$encounterQuestion = $em->getRepository('GeneralGeneralBundle:EncounterQuestion')->findOneBy(array('id' => $id));
		
		//get EncounterRating entity
		$encounterRating = $em->getRepository('GeneralGeneralBundle:EncounterRating')->findOneBy(array('encounterQuestion' => $encounterQuestion));
		
		$requesteruser = $encounterRating->getRequester();
		$answereruser = $encounterRating->getAnswerer();
		
		//get third party user, that is to be rated
		if($user==$requesteruser)
		{
			$otheruser = $answereruser;
		}
		else 
			$otheruser = $requesteruser;
		
		//build search form
		$encounterRating = new EncounterRating();
		$encounterRatingAnalytics = $this->container->getParameter('encounterratinganalytics.properties');
		$encounterRatingChoices = $this->container->getParameter('encounterratinganalytics.choices');
		$form = $this->createForm(new EncounterRatingType($encounterRatingAnalytics, $encounterRatingChoices), $encounterRating, array(
				'action' => $this->generateUrl('aristos_core_encounter_rate', array('id'=>$id)),
		));
	
		return $this->render('AristosCoreBundle:Voting:rate.html.twig', array(
				'form' => $form->createView(),
				'encounterQuestion' => $encounterQuestion,
				'type' => $type,
				'otheruser' => $otheruser,
				'notifications' => $notifications
		));
	
	
		
	
	}

  
}
