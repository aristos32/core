<?php 
namespace Aristos\CoreBundle\Classes;

use Aristos\CoreBundle\Entity\Tag;

/**
 * General functions
 */

class Functions
{
		
		
	
	public static function slugify($text)
    {
    	// replace non letter or digits by -
    	$text = preg_replace('#[^\\pL\d]+#u', '-', $text);
    
    	// trim
    	$text = trim($text, '-');
    
    	// transliterate
    	if (function_exists('iconv'))
    	{
    		$text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    	}
    
    	// lowercase
    	$text = strtolower($text);
    
    	// remove unwanted characters
    	$text = preg_replace('#[^-\w]+#', '', $text);
    
    	if (empty($text))
    	{
    		return 'n-a';
    	}
    
    	return $text;
    }
    
    /*
     * generate random string 
     * for username
     */
    public static function generateRandomString($length = 10) {
    	return substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
    }
    
    /*
     * update the table with new, or existing tags
    * @param: tags new tags to add
    * @param: oldTags - previous tags to remove, when updating an entity
    * $em : send doctrine entity manager, since it's hard to get it here
    */
    public static function updateTagSexTable($tags, $oldTags, $em)
    {
    	//$em = $this->getDoctrine()->getManager();
    
    	//update tags table
    	foreach ($tags as $eachTag)
    	{
    		$eachTag = trim($eachTag);
    		 
    		$tag = $em->getRepository('AristosCoreBundle:Tag')->findOneBy(array('name' => $eachTag) );
    		if(!$tag)
    		{
    			$tag = new Tag();
    			$tag->setName($eachTag);
    			$tag->setApproved(false);
    			$tag->setDescription($eachTag);
    			$tag->setTimesUsed(1);
    			$em->persist($tag);
    		}
    		else
    		{
    			$tag->setTimesUsed($tag->getTimesUsed() + 1 );
    			$em->merge($tag);
    		}
    	}
    
    	$em->flush();
    
    	//remove old tags - update entity case
    	foreach ($oldTags as $eachTag)
    	{
    		$tag = $em->getRepository('AristosCoreBundle:Tag')->findOneBy(array('name' => $eachTag) );
    		$tag->setTimesUsed($tag->getTimesUsed() - 1 );
    		$em->merge($tag);
    	}
    	$em->flush();
    }
}