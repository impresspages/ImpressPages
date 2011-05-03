<?php
/**
 * @package	ImpressPages
 * @copyright	Copyright (C) 2011 ImpressPages LTD.
 * @license	GNU/GPL, see ip_license.html
 */
namespace Modules\standard\menu_management;

if (!defined('BACKEND')) exit;



require_once(__DIR__.'/model.php');
require_once(__DIR__.'/template.php');


class BackendWorker {


  function __construct() {

  }


  function work() {
    global $parametersMod;
    global $site;

    if (!isset($_REQUEST['action']) ) {
      return;
    }

    switch ($_REQUEST['action']) {
      case 'getChildren' :
        $this->_getChildren ();
        break;
      case 'getUpdatePageForm' :
        $this->_getPageForm();
        break;
      case 'getPageLink' :
        $this->_getPageLink();
        break;        
      case 'updatePage' :
        $this->_updatePage();
        break;        
      case 'getCreatePageForm' :
        $this->_getCreatePageForm();
        break;        
      case 'createPage' :
        $this->_createPage();
        break;        
    }

  }

  
  
  private function _getChildren () {
    $parentType = isset($_REQUEST['type']) ? $_REQUEST['type'] : null;
    $parentWebsiteURL = isset($_REQUEST['websiteURL']) ? $_REQUEST['websiteURL'] : null;
    $parentLanguageId = isset($_REQUEST['languageId']) ? $_REQUEST['languageId'] : null;
    $parentZoneName = isset($_REQUEST['zoneName']) ? $_REQUEST['zoneName'] : null;
    $parentId = isset($_REQUEST['id']) ? $_REQUEST['id'] : null;

    $list = $this->_getList ($parentType, $parentWebsiteURL, $parentLanguageId, $parentZoneName, $parentId);

    $this->_printJson ($list);
  }
  
  private function _getList ($parentType, $parentWebsiteURL, $parentLanguageId, $parentZoneName, $parentId) {
    global $site;

    $answer = array();
    
    switch ($parentType) {
      case '' : //return websites

        if ($parentId == null || $parentId == '') {
      		
          $answer[] = array(
    				'attr' => array('id' => BASE_URL, 'rel' => 'website', 'websiteURL' => BASE_URL),
    				'data' => BASE_URL,
    				'state' => 'open',
          	'children' => $this->_getList($parentType = 'website', $parentWebsiteURL = BASE_URL, $parentLanguageId = null, $parentZoneName = null, $parentId = BASE_URL)
      		);
        }
        
        break;
      case 'website' : //return languages
        if ($parentId == BASE_URL) {
          $languages = Model::getLanguages();
          foreach ($languages as $languageKey => $language) {
            $answer[] = array(
      				'attr' => array('id' => $language['id'], 'rel' => 'language', 'websiteURL' => $parentWebsiteURL, 'languageId' => $language['id']),
      				'data' => $language['d_short'] . '', //transform null into empty string. Null break JStree into infinite loop 
      				'state' => 'closed'
        		);
          }
          
        }
        
        break;
      case 'language' : //return zones
        
        if (empty($parentWebsiteURL)) {
          trigger_error("Website URL is not set");
          return false;
        }
        $websiteURL = $_REQUEST['websiteURL'];
        
        if (empty($parentLanguageId)) {
          trigger_error("Language id is not set");
          return false;
        }
                
        $zones = Model::getZones();        
        
        foreach ($zones as $zoneKey => $zone) {
          $zoneElement = Model::rootContentElement($zone['id'], $parentId);

          if($zoneElement == null) { /*try to create*/
            Model::createRootZoneElement($zone['id'], $parentId);
            $zoneElement = Model::rootContentElement($zone['id'], $parentId);
            if($zoneElement == null) {	/*fail to create*/
              trigger_error("Can't create root zone element.");
              return false;
            }
          }
          
          
          $answer[] = array (
    				'attr' => array('id' => $zoneElement['id'], 'rel' => 'zone', 'websiteURL' => $parentWebsiteURL, 'languageId' => $parentLanguageId, 'zoneName' => $zone['name']),
    				'data' => $zone['title'] . '', //transform null into empty string. Null break JStree into infinite loop 
    				'state' => 'closed'
        	);
        } 

        break;
      case 'zone' : //return pages
      case 'page' : //return pages
        
        if (empty($parentZoneName)) {
          trigger_error("Zone name is not set");
          return false;
        }
        
        if (empty($parentWebsiteURL)) {
          trigger_error("Website URL is not set");
          return false;
        }
        
        if (empty($parentLanguageId)) {
          trigger_error("Language Id is not set");
          return false;
        }
        
        $children = Model::contentElementChildren($parentId);
        foreach($children as $childKey => $child) {

          if ($child['visible'])
            $disabled = 0;
          else
            $disabled = 1;
          
          $answer[] = array (
    				'attr' => array('id' => $child['id'], 'rel' => 'page', 'disabled' => $disabled, 'websiteURL' => $parentWebsiteURL, 'languageId' => $parentLanguageId, 'zoneName' => $parentZoneName),
    				'data' => $child['button_title'] . '', //transform null into empty string. Null break JStree into infinite loop 
    				'state' => 'closed'
        	);
          
        }
        break;        
      default :
        trigger_error('Unknown type '.$parentType);
        return false;
        break;
    }
    
    return $answer;
  }

  
  
  private function _getPageForm() {
    global $site;
    global $parametersMod;
    
    if (!isset($_REQUEST['id'])) {
      trigger_error("Element id is not set");
      return;
    }
    
    $elementId = $_REQUEST['id'];
    
    if (empty($_REQUEST['zoneName'])) {
      trigger_error("Zone name is not set");
      return false;
    }        
    
    $zone = $site->getZone($_REQUEST['zoneName']);
    
    if (empty($zone)) {
      trigger_error("Can't find zone");
      return false;
    }
      
    $element = $zone->getElement($elementId);
    
    if (! $element) {
      trigger_error ("Page does not exist");
      return false;
    }
    
    $tabs = array(); 
    

    $title = $parametersMod->getValue('standard', 'menu_management', 'admin_translations', 'general');
    $content = Template::generateTabGeneral();
    $tabs[] = array('title' => $title, 'content' => $content);
    
    $title = $parametersMod->getValue('standard', 'menu_management', 'admin_translations', 'seo');
    $content = Template::generateTabSEO();
    $tabs[] = array('title' => $title, 'content' => $content);
    
    $title = $parametersMod->getValue('standard', 'menu_management', 'admin_translations', 'advanced');
    $content = Template::generateTabAdvanced();
    $tabs[] = array('title' => $title, 'content' => $content);
    
    
    $answer = array();
    $answer['page'] = array(); 

    
    $answer['page']['id'] = $element->getId(); 
    $answer['page']['zoneName'] = $element->getZoneName(); 
    $answer['page']['buttonTitle'] = $element->getButtonTitle() . ''; 
    $answer['page']['visible'] = $element->getVisible(); 
    $answer['page']['createdOn'] = $element->getCreatedOn(); 
    $answer['page']['lastModified'] = $element->getLastModified();
     
    $answer['page']['pageTitle'] = $element->getPageTitle() . ''; 
    $answer['page']['keywords'] = $element->getKeywords() . ''; 
    $answer['page']['description'] = $element->getDescription() . ''; 
    $answer['page']['url'] = $element->getUrl() . '';
     
    $answer['page']['type'] = $element->getType(); 
    $answer['page']['redirectURL'] = $element->getRedirectUrl() . ''; 

    $answer['html'] = Template::generatePageProperties($tabs);
    
    $this->_printJson ($answer);    
  }
  
  
  private function _getCreatePageForm() {
    global $site;
    global $parametersMod;
    
    if (!isset($_REQUEST['id'])) {
      trigger_error("Element id is not set");
      return;
    }
    
    $parentElementId = $_REQUEST['id'];
    
    if (empty($_REQUEST['zoneName'])) {
      trigger_error("Zone name is not set");
      return false;
    }        
    
    $zoneName = $_REQUEST['zoneName'];
    $zone = $site->getZone($_REQUEST['zoneName']);
    
    if (empty($zoneName)) {
      trigger_error("Can't find zone");
      return false;
    }
      
    $parentElement = $zone->getElement($parentElementId);
    
    if (! $parentElement) {
      trigger_error ("Page does not exist");
      return false;
    }
    
    $tabs = array(); 

    $element = new \Frontend\Element('', $_REQUEST['zoneName']);
    
    if($parametersMod->getValue('standard', 'menu_management', 'options', 'hide_new_pages')) {
      $element->setVisible(!$parametersMod->getValue('standard', 'menu_management', 'options', 'hide_new_pages'));
    }   

    $title = $parametersMod->getValue('standard', 'menu_management', 'admin_translations', 'general');
    $content = Template::generateTabGeneral($element, $parentElement);
    $tabs[] = array('title' => $title, 'content' => $content);
    
    $title = $parametersMod->getValue('standard', 'menu_management', 'admin_translations', 'seo');
    $content = Template::generateTabSEO($element, $parentElement);
    $tabs[] = array('title' => $title, 'content' => $content);
    
    $title = $parametersMod->getValue('standard', 'menu_management', 'admin_translations', 'advanced');
    $content = Template::generateTabAdvanced($element, $parentElement);
    $tabs[] = array('title' => $title, 'content' => $content);
    
    
    $answer = array();
    
    $element = new \Frontend\Element('', $zoneName);
    $element->setCreatedOn(date('Y-m-d'));
    $element->setLastModified(date('Y-m-d'));
    $element->setType('default');
    
    $answer['parent']['id'] = $parentElementId;
    $answer['page']['id'] = $element->getId(); 
    $answer['page']['zoneName'] = $element->getZoneName(); 
    $answer['page']['buttonTitle'] = $element->getButtonTitle(); 
    $answer['page']['visible'] = $element->getVisible(); 
    $answer['page']['createdOn'] = $element->getCreatedOn(); 
    $answer['page']['lastModified'] = $element->getLastModified();
     
    $answer['page']['pageTitle'] = $element->getPageTitle(); 
    $answer['page']['keywords'] = $element->getKeywords(); 
    $answer['page']['description'] = $element->getDescription(); 
    $answer['page']['url'] = $element->getUrl();
     
    $answer['page']['type'] = $element->getType(); 
    $answer['page']['redirectURL'] = $element->getRedirectUrl(); 
        
    $answer['html'] = Template::generatePageProperties($tabs);
    
    $this->_printJson ($answer);    
  }  
  
  
  private function _getPageLink() {
    global $site;
    $answer = array();
    
    if (empty($_REQUEST['websiteURL'])) {
      trigger_error("Website URL is not set");
      return false;
    }           

    if (empty($_REQUEST['type'])) {
      trigger_error("Page type is not set");
      return false;
    }           
    
    $type = $_REQUEST['type'];
    
    switch ($_REQUEST['type']) {
      case 'website':
        $answer['link'] = $_REQUEST['websiteURL'];
        break;
      case 'language':
        if (empty($_REQUEST['languageId'])) {
          trigger_error("Language Id is not set");
          return false;
        }
        $answer['link'] = $site->generateUrl($_REQUEST['languageId']);
        break;
        
      case 'zone':
        if (empty($_REQUEST['languageId'])) {
          trigger_error("Language Id is not set");
          return false;
        }
        if (empty($_REQUEST['zoneName'])) {
          trigger_error("Zone name is not set");
          return false;
        }
        
        $answer['link'] = $site->generateUrl($_REQUEST['languageId'], $_REQUEST['zoneName']);
        
        break;
      case 'page':
        if (empty($_REQUEST['languageId'])) {
          trigger_error("Language Id is not set");
          return false;
        }
        if (empty($_REQUEST['zoneName'])) {
          trigger_error("Zone name is not set");
          return false;
        }
        if (empty($_REQUEST['id'])) {
          trigger_error("Page Id is not set");
          return false;
        }
        
        $elementId = $_REQUEST['id'];
        $zone = $site->getZone($_REQUEST['zoneName']);
        
        if (! $zone) {
          trigger_error("Ca'nt find zone");
          return false;
        }
        
        $element = $zone->getElement($elementId);
        
        if (! $element) {
          trigger_error("Can't find element");
          return false;
        }

        $answer['link'] = $element->getLink();
        
        
        break;
        
        
      default: 
        trigger_error('Undefined page type');
        return false;
    }
            
    $this->_printJson ($answer);    
  }
  
  
  private function _updatePage () {
    global $parametersMod;
    global $site;
    
    $answer = array();
    

    //make url
    if ($_POST['url'] == '') {
      if ($_POST['pageTitle'] != '') {
        $_POST['url'] = Model::makeUrl($_POST['pageTitle'], $_POST['pageId']);
      } else {
        $_POST['url'] = Model::makeUrl($_POST['buttonTitle'], $_POST['pageId']);
      }
    } else {
      $tmpUrl = str_replace("/", "-", $_POST['url']);
      $i = 1;
      while (!Model::availableUrl($tmpUrl, $_POST['pageId'])) {
        $tmpUrl = $_POST['url'].'-'.$i;
        $i++;
      }
      $_POST['url'] = $tmpUrl;
    }
    //end make url

    if (strtotime($_POST['createdOn']) === false) {
      $answer['errors'][] = array('field' => 'createdOn', 'message' => $parametersMod->getValue('standard', 'menu_management', 'admin_translations', 'error_date_format').date("Y-m-d"));
    }

    if (strtotime($_POST['lastModified']) === false) {
      $answer['errors'][] = array('field' => 'lastModified', 'message' => $parametersMod->getValue('standard', 'menu_management', 'admin_translations', 'error_date_format').date("Y-m-d"));
    }

    if ($_POST['type'] == 'redirect' && $_POST['redirectURL'] == '') {
      $answer['errors'][] = array('field' => 'redirectURL', 'message' => $parametersMod->getValue('standard', 'menu_management', 'admin_translations', 'error_type_url_empty'));
    }


    if (empty($answer['errors'])) {
      $zone = $site->getZone($_POST['zoneName']);
      $oldElement = $zone->getElement($_POST['pageId']);

      Model::updateContentElement($_POST['pageId'], $_POST);

      if($oldElement->getUrl() != $_POST['url']){
        $newElement = $zone->getElement($_POST['pageId']);
        $site->dispatchEvent('administrator', 'system', 'url_change', array('old_url'=>$oldElement->getLink(true), 'new_url'=>$newElement->getLink(true)));
      }

    }
    
    $this->_printJson ($answer);
  } 
  
  

  private function _createPage () {
    global $parametersMod;
    global $site;
    
    $answer = array();
    

    //make url
    if ($_POST['url'] == '') {
      if ($_POST['pageTitle'] != '') {
        $_POST['url'] = Model::makeUrl($_POST['pageTitle']);
      } else {
        $_POST['url'] = Model::makeUrl($_POST['buttonTitle']);
      }
    } else {
      $tmpUrl = str_replace("/", "-", $_POST['url']);
      $i = 1;
      while (!Model::availableUrl($tmpUrl)) {
        $tmpUrl = $_POST['url'].'-'.$i;
        $i++;
      }
      $_POST['url'] = $tmpUrl;
    }
    //end make url
    
    
    if (strtotime($_POST['createdOn']) === false) {
      $answer['errors'][] = array('field' => 'createdOn', 'message' => $parametersMod->getValue('standard', 'menu_management', 'admin_translations', 'error_date_format').date("Y-m-d"));
    }

    if (strtotime($_POST['lastModified']) === false) {
      $answer['errors'][] = array('field' => 'lastModified', 'message' => $parametersMod->getValue('standard', 'menu_management', 'admin_translations', 'error_date_format').date("Y-m-d"));
    }

    if ($_POST['type'] == 'redirect' && $_POST['redirectURL'] == '') {
      $answer['errors'][] = array('field' => 'redirectURL', 'message' => $parametersMod->getValue('standard', 'menu_management', 'admin_translations', 'error_type_url_empty'));
    }
    
    if (empty($answer['errors'])) {
    
      $_POST['createdOn'] = date("Y-m-d", strtotime($_POST['createdOn']));
      $_POST['lastModified'] = date("Y-m-d", strtotime($_POST['lastModified']));
  
      $visible = $_POST['visible'];

      $newPageId = Model::insertContentElement($_POST['parentId'], $_POST);

      $answer['status'] = 'success';
      $answer['page']['id'] = $newPageId;
    }
    
    $this->_printJson ($answer);
  }   
  
  private function _printJson ($data) {
    header("HTTP/1.0 200 OK");
    header('Content-type: text/json; charset=utf-8');
    header("Cache-Control: no-cache, must-revalidate");
    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    header("Pragma: no-cache");
    echo json_encode($data);

  }


  




}