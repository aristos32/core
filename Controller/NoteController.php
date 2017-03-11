<?php
namespace Aristos\CoreBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use General\GeneralBundle\Controller\MyBaseController;
use Aristos\CoreBundle\Form\NoteType;
use Aristos\CoreBundle\Entity\Note;
/**
 * controller.
 */
class NoteController extends MyBaseController
{
		
	/**
	 * create a new note
	 *  
	 * @param id : questionid
	 * @param type : type of user rating, {requester, answerer}
	 * 
	 */
	public function createAction($id)
	{
		//echo __METHOD__.$id;exit;

		$errormessage = "";
	
		$em = $this->getDoctrine ()->getManager();
		
		$request = $this->getRequest();
		$title = $request->request->get('core_note')['title'];
		$description = $request->request->get('core_note')['description'];
	
		// get the logged in user
		$loggeduser = $this->get ( 'security.context' )->getToken ()->getUser ();

		// get pending notifications
		$notifications = $this->getNotifications ( $loggeduser );
		
		$question = $em->getRepository('GeneralGeneralBundle:EncounterQuestion')->findOneBy(array('id' => $id));
		
		$note = new Note();
		$note->setEncounterquestion($question);
		$note->setUser($loggeduser);
		
		$form    = $this->createForm(new NoteType(), $note);
		$form->bind($request);
		 
		if ($form->isValid()) {
		
			//Persist the entity
			$note = $form->getData();
			$em->persist($note);
			$em->flush();

			//var_dump($description); exit;
			//echo __METHOD__.$id;exit;
		}
		//get form errors for debugging
		else
		{
			//echo __METHOD__." error";
			//var_dump($form->getErrorsAsString());exit;
			//set a flash message
			$this->get('session')->getFlashBag()->add('error', $form->getErrorsAsString());
		}
		
		$notes = $em->getRepository('AristosCoreBundle:Note')->findBy(array('encounterquestion'=>$question));
			
		return $this->redirect($this->generateUrl('GeneralGeneralBundle_encounterquestion_show', array(
				'id' => $question->getId() ,
				'slug'  => $question->getSlug(),
				'notes' => $notes,
				'notifications' => $notifications))
		);
	
	}
	
	/**
	 *
	 * update a note
	 * @param id : note id
	 * @param type : type of user rating, {requester, answerer}
	 *
	 */
	public function updateAction($id)
	{
		//echo __METHOD__.$id;exit;
	
		$errormessage = "";
	
		$em = $this->getDoctrine ()->getManager();
	
		$request = $this->getRequest();
		$description = $request->request->get('description');
	
		// get the logged in user
		$loggeduser = $this->get ( 'security.context' )->getToken ()->getUser ();
	
		// get pending notifications
		$notifications = $this->getNotifications ( $loggeduser );
	
		$note = $em->getRepository('AristosCoreBundle:Note')->findOneBy(array('id' => $id));
		$note->setDescription($description);

		//a user that answered the question, may also edit a note
		$answers = $note->getEncounterquestion()->getAnswers();
		$editNote = false;
		foreach($answers as $eachAnswer)
		{
			$eachUser = $eachAnswer->getUser();
			if($loggeduser==$eachUser){
				$editNote = true;
				break;
			}
		}
		
		$em->persist($note);
		$em->flush();
	
		$errormessage = "Description updated";
		
		$return=array("responseCode"=>200,  "errormessage"=>$errormessage);
		
		$return=json_encode($return);//jscon encode the array
		return new Response($return,200,array('Content-Type'=>'application/json'));//make sure it has the correct content type
	
	}
	
	/**
	 * remove a note
	 * @param id : note id
	 *
	 */
	public function removeAction(Request $request)
	{
		$noteid = $request->get('noteid');
		
		$em = $this->getDoctrine()->getManager();
		$note = $em->getRepository('AristosCoreBundle:Note')->findOneBy(array('id' => $noteid));
		
		if ($note != null){
			$em->remove($note);
			$em->flush();
		}
		
		$errormessage = "Note removed";
		$return=array("responseCode"=>200,  "errormessage"=>$errormessage);
		$return=json_encode($return);//jscon encode the array
		return new Response($return,200,array('Content-Type'=>'application/json'));//make sure it has the correct content type
	
	}

  
}
