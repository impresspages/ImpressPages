<?php 
/**
 * @package	ImpressPages
 * @copyright	Copyright (C) 2011 ImpressPages LTD.
 * @license	GNU/GPL, see ip_license.html
 */
 
namespace Modules\community\newsletter;

 
if (!defined('CMS')) exit;  


/**
 * 
 *   
 * @package ImpressPages
 */ 

class Element extends \Frontend\Element{

  public function getLink(){
    global $site;
    return $site->generateUrl(null, $this->zoneName, array($this->getId()));
  }
  
  public function getDepth(){
    return 1;
  } 
  
  
  public function getButtonTitle(){
    global $parametersMod;
    return $parametersMod->getValue('community', 'newsletter', 'subscription_translations', 'newsletter');
  }
    
	public function generateContent(){
		global $site;
		global $parametersMod;
		
		$text = '';
		switch($this->getId()){
			case 'email_confirmation':
				$text = $parametersMod->getValue('community', 'newsletter', 'subscription_translations', 'text_confirmation');
			break;
			case 'incorrect_email':
				$text = $parametersMod->getValue('community', 'newsletter', 'subscription_translations', 'text_incorrect_email');
			break;
			case 'subscribed':
				$text = $parametersMod->getValue('community', 'newsletter', 'subscription_translations', 'text_subscribed');
			break;
			case 'unsubscribed':
				$text = $parametersMod->getValue('community', 'newsletter', 'subscription_translations', 'text_unsubscribed');
			break;
			case 'error_confirmation':
				$text = $parametersMod->getValue('community', 'newsletter', 'subscription_translations', 'text_error_confirmation');
			break;
			case 'unsubscribe':
				$text = '';			
			break;
			case null:
			  $text = '';
			break;
		}
		
		
		$site->requireTemplate('community/newsletter/template.php');
		$template = new Template();		
		$answer = $template->textPage($text);
		return $answer;    
	}
	
	
	public function generateManagement(){
		return $this->generateContent();
	}  
}




 