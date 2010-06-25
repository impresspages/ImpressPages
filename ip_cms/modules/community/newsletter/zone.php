<?php 
/**
 * @package		ImpressPages
 * @copyright	Copyright (C) 2009 JSC Apro media.
 * @license		GNU/GPL, see ip_license.html
 */

namespace Modules\community\newsletter;

if (!defined('FRONTEND')&&!defined('BACKEND')) exit;


/** @private */
require_once (__DIR__.'/element.php');
/** @private */

class Zone extends \Frontend\Zone{
  var $zoneName;
  function __construct($key){
    $this->zoneName = $key;
  }


  /**
   * Finds all pages of current zone      
   * @return array elements   
   */  
  public function getElements($language = null, $parentElementId = null, $startFrom = 1, $limit = null, $includeHidden = false, $reverseOrder = null){
    return array();
  }
  

  /**
   * @param int $elementId       
   * @return array element   
   */
	public  function getElement($elementId){
    switch($elementId){
      case 'email_confirmation':
      case 'incorrect_email':
      case 'subscribed':
      case 'unsubscribed':
      case 'error_confirmation':
      case 'unsubscribe':
       return new Element($elementId, $this->name); //default zone return element with all url and get variable combinations
    }
	  return false;
	}
	
  /**
   * @param int $elementId       
   * @return string link to specified element   
   */  
	public function generateUrl($elementId){
	  return false;
	}

	
  /**
   * @param array $urlVars        
   * @return array element   
   */	
  public function findElement($urlVars, $getVars){
	 /*this zone never returns error404 and in reality have no pages (elements)*/
	  global $site;
    if(isset($site->urlVars[0])){	  
			switch($site->urlVars[0]){
				case 'email_confirmation':
				case 'incorrect_email':
				case 'subscribed':
				case 'unsubscribed':
				case 'error_confirmation':
				case 'unsubscribe':
      	 return new Element($site->urlVars[0], $this->zoneName);
      }
    }
    return (new Element(null, $this->zoneName));
  }
  
  public function generateRegistrationBox(){
    global $site;
    global $parametersMod;
    
    $site->requireTemplate('community/newsletter/template.php');  
    return Template::registration($site->generateUrl(null, $this->zoneName), $parametersMod->getValue('community', 'newsletter', 'options', 'show_unsubscribe_button'));
  }
  
  public function makeActions(){
    require_once(__DIR__.'/actions.php');
    $actions = Actions::makeActions($this->zoneName);
  }

}
