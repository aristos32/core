<?php 
namespace Aristos\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Aristos\CoreBundle\Entity\Contacts;
use Aristos\CoreBundle\Entity\ContactRequest;
use Symfony\Component\HttpFoundation\Response;
use General\GeneralBundle\Controller\MyBaseController;

/**
 * Contacts controller.
 */
class ContactRequestController extends MyBaseController
{
	/*
	 * show all pending contacts request of a user
	 */
    public function allAction()
    {   	
    	//echo __METHOD__.": inside";exit;

    	$user = $this->get('security.context')->getToken()->getUser();
    	
    	//get pending notifications
    	$notifications = $this->getNotifications($user);
    	
    	//contact requests received by user
    	$contactsRequests = $this->getContactRequestUsers();
    	
    	//\Doctrine\Common\Util\Debug::dump($contactUsers);exit;
    	 
    	return $this->render('AristosCoreBundle:ContactRequest:show.html.twig',
    			array('contactsRequests' => $contactsRequests, 'user'=>$user,
    	'notifications' => $notifications));
    	
    	
    }
    
    
    
    /*
     * called by ajax, when changing a contact request status in twig template
     * @param senderUserid sender of contact request
     * @param receiverUserid receiver of contact request
    */
    public function updateAction()
    {
    	//echo "here";exit;
    	$em = $this->getDoctrine()->getManager();
    	$errormessage = "";
    	 
    	$request = $this->get('request');
    	$contactRequestStatus=$request->request->get('contactStatus');
    	$senderUserid=$request->request->get('senderUserid');
    	$receiverUserid=$request->request->get('receiverUserid');
    	$otherUser = $em->getRepository('AristosCoreBundle:User')->findOneBy(array('id' => $senderUserid));
    	
    	//var_dump($otherUser);exit;
    	//\Doctrine\Common\Util\Debug::dump($receiverUserid);exit;
    	
    	$user = $em->getRepository('AristosCoreBundle:User')->findOneBy(array('id' => $receiverUserid));
    	
    	
    	
    	//change contact status
    	$existingContactRequest = $em->getRepository ( 'AristosCoreBundle:ContactRequest' )->findOneBy ( array (
    			'senderUser' => $otherUser,
    			'receiverUser' => $user
    	) );
    	
    	
    	
    	$existingContactRequest->setStatus($contactRequestStatus);
    	$em->merge($existingContactRequest);
    	$em->flush();
    	
    	$existingContact = $em->getRepository ( 'AristosCoreBundle:Contacts' )->findOneBy ( array (
    			'currentUser' => $user,
    			'otherUser' => $otherUser));
    	

    	
    	
    	//add request to contacts
    	if($contactRequestStatus=='accepted')
    	{
    		
    		if(empty($existingContact))
    		{
    		
    		
    			//add contact for logged user
    			$newContact = new Contacts();
    			$newContact->setCurrentUser($user);
    			$newContact->setOtherUser($otherUser);
    			$em->persist($newContact);
    			$em->flush();
    			    		
    			//add contact for sender user
    			$newContact = new Contacts();
    			$newContact->setCurrentUser($otherUser);
    			$newContact->setOtherUser($user);
    			$em->persist($newContact);
    			$em->flush();
    			//echo $senderUserid.$contactStatus;exit;
    		}
    		
    		//contact already exists - update status
    		else
    		{
    			$existingContact->setStatus('active');
    			$em->merge($existingContact);
    			$em->flush();
    			
    			$existingContact = $em->getRepository ( 'AristosCoreBundle:Contacts' )->findOneBy ( array (
    					'currentUser' => $otherUser,
    					'otherUser' => $user));
    			$existingContact->setStatus('active');
    			$em->merge($existingContact);
    			$em->flush();
    		
    		}
    		
    	}
    	
    	
    	   	
    	//return $this->redirect($this->generateUrl('GeneralGeneralBundle_contact_request'));

    	$errormessage = "Contact Request Status updated";
    
    	$return=array("responseCode"=>200,  "errormessage"=>$errormessage);
    
    	$return=json_encode($return);//jscon encode the array
    	return new Response($return,200,array('Content-Type'=>'application/json'));//make sure it has the correct content type
    
    }

   

   
}