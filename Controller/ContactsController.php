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
class ContactsController extends MyBaseController
{
	/*
     * called by ajax, when changing a contact status in twig template
     */
    public function updateAction()
    {
    	$em = $this->getDoctrine()->getManager();
    	$errormessage = "";
    	
    	$request = $this->get('request');
		$contactStatus=$request->request->get('contactStatus');
		$contactUserid=$request->request->get('contactUserid');
		$otherUser = $em->getRepository('AristosCoreBundle:User')->findOneBy(array('id' => $contactUserid));

		$user = $this->get('security.context')->getToken()->getUser();
		$userid = $user->getId();
		
		//update contact from current user
		$existingContact = $em->getRepository ( 'AristosCoreBundle:Contacts' )->findOneBy ( array (
				'currentUser' => $user,
				'otherUser' => $otherUser
		) );
		
		if(!empty($existingContact))
		{
			$existingContact->setStatus($contactStatus);				
			$em->merge($existingContact);
		}
		
		
		//update contact from other user
		$existingContact = $em->getRepository ( 'AristosCoreBundle:Contacts' )->findOneBy ( array (
				'currentUser' => $otherUser,
				'otherUser' => $user
		) );
		
    	if(!empty($existingContact))
		{
			$existingContact->setStatus($contactStatus);				
			$em->merge($existingContact);
		}
		
		$em->flush();
		
		$errormessage = "Status updated";
		
		$return=array("responseCode"=>200,  "errormessage"=>$errormessage);
				
		$return=json_encode($return);//jscon encode the array
		return new Response($return,200,array('Content-Type'=>'application/json'));//make sure it has the correct content type
		
    }
    
    /*
     * called to send a contact request
     */
    public function requestAction()
    {
    	//echo "insdde";exit;
    	$em = $this->getDoctrine()->getManager();
    	$errormessage = "";
    	
    	$request = $this->get('request');
		$contactUserid = $request->request->get('contactUserid');
		$otherUser = $em->getRepository('AristosCoreBundle:User')->findOneBy(array('id' => $contactUserid));
		
		$currentUser = $this->get('security.context')->getToken()->getUser();
		
		//check if already sent a request
		$contactRequest = $em->getRepository ( 'AristosCoreBundle:ContactRequest' )->findOneBy ( array (
							'senderUser' => $currentUser,
							'receiverUser' => $otherUser
					) );
		if ($contactRequest)
		{
			$errormessage = "Request Already Sent";
			$return=array("responseCode"=>200,  "errormessage"=>$errormessage);
			$return=json_encode($return);//jscon encode the array
			return new Response($return,200,array('Content-Type'=>'application/json'));//make sure it has the correct content type
		}
			
		
		$newContactRequest = new ContactRequest();
		$newContactRequest->setSenderUser($this->get('security.context')->getToken()->getUser());
		$newContactRequest->setReceiverUser($otherUser);
		$em->persist($newContactRequest);
		
		try {
		    $em->flush();
		}
		catch( \Exception $e )
		{
			//echo $e->getCode();
			//echo $e->getMessage();
			$errormessage = "Cannot send request";		
			$return=array("responseCode"=>400,  "errormessage"=>$errormessage);					
			$return=json_encode($return);//jscon encode the array
			return new Response($return,200,array('Content-Type'=>'application/json'));//make sure it has the correct content type
		}
		
		$errormessage = "Contact Request Sent";
		$return=array("responseCode"=>200,  "errormessage"=>$errormessage);
		$return=json_encode($return);//jscon encode the array
		return new Response($return,200,array('Content-Type'=>'application/json'));//make sure it has the correct content type
        
    }

   
}