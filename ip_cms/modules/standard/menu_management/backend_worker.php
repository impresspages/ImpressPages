<?php
/**
 * @package		ImpressPages
 * @copyright	Copyright (C) 2009 JSC Apro media.
 * @license		GNU/GPL, see ip_license.html
 */
namespace Modules\standard\menu_management;  

if (!defined('BACKEND')) exit; 




require_once (BASE_DIR.MODULE_DIR.'standard/content_management/db.php');
require_once (BASE_DIR.FRONTEND_DIR.'site.php');
require_once (BASE_DIR.FRONTEND_DIR.'db.php');
require_once (BASE_DIR.FRONTEND_DIR.'zone.php');
require_once (BASE_DIR.MODULE_DIR.'standard/content_management/zone.php');
require_once (__DIR__.'/db.php');
require_once (BASE_DIR.LIBRARY_DIR.'php/js/functions.php');

class BackendWorker {
  var $notes;
  var $errors;
  var $variables;
  function __construct() {

    $this->notes = array();
    $this->errors = array();

    $this->variables = array();
  }


  function work() {
    if (isset($_REQUEST['action']))
      switch($_REQUEST['action']) {
        case "get_page":
          $this->getPage();
          break;
        case "update_page":
          $this->updatePage();
          break;
        case "new_page":
          $this->newPage();
          break;
        default:
          echo 'alert(\'No action\');';
          break;
      }
  }


  public function newPage() {
    global $parametersMod;


    if (strtotime($_POST['created_on']) === false) {
      $errorCreatedOn = true;
    } else {
      $errorCreatedOn = false;
    }

    if (strtotime($_POST['last_modified']) === false) {
      $errorLastModified = true;
    } else {
      $errorLastModified = false;
    }

    if ($_POST['type'] == 'redirect' && $_POST['redirect_url'] == '') {
      $errorEmptyRedirectUrl = true;
    } else {
      $errorEmptyRedirectUrl = false;
    }


    if ($errorCreatedOn || $errorLastModified || $errorEmptyRedirectUrl) {
      $answer = '';
      if($errorCreatedOn)
        $answer .= 'document.getElementById(\'property_created_on_error\').style.display = \'block\';';

      if($errorLastModified)
        $answer .= 'document.getElementById(\'property_last_modified_error\').style.display = \'block\';';

      if($errorEmptyRedirectUrl)
        $answer .= 'document.getElementById(\'property_type_error\').style.display = \'block\';';

      $answer .= 'document.getElementById(\'loading\').style.display = \'none\';';
      echo $answer;

    } else {
      //make url
      if ($_POST['url'] == '') {
        if ($_POST['page_title'] != '') {
          $_POST['url'] = Db::makeUrl($_POST['page_title']);
        } else {
          $_POST['url'] = Db::makeUrl($_POST['button_title']);
        }
      } else {
        $tmpUrl = str_replace("/", "-", $_POST['url']);
        $i = 1;
        while (!Db::availableUrl($tmpUrl)) {
          $tmpUrl = $_POST['url'].'-'.$i;
          $i++;
        }
        $_POST['url'] = $tmpUrl;
      }
      //end make url

      $_POST['created_on'] = date("Y-m-d", strtotime($_POST['created_on']));
      $_POST['last_modified'] = date("Y-m-d", strtotime($_POST['last_modified']));
      $newNodeIndex = sizeof(\Modules\standard\content_management\Db::menuElementChildren($_POST['parent_id']));

      $visible = $_POST['visible'];
      /*if($parametersMod->getValue('standard', 'menu_management', 'options', 'hide_new_pages'))
        $visible = '0';
      else
        $visible = '1';*/

      $newNodeId = Db::insertContentElement($_POST['parent_id'], $newNodeIndex, $visible, $_POST);

      //js answer
      echo '
       document.getElementById(\'loading\').style.display = \'none\';
       
      document.getElementById(\'property_last_modified_error\').style.display = \'none\';
      document.getElementById(\'property_created_on_error\').style.display = \'none\';      
      document.getElementById(\'property_type_error\').style.display = \'none\';       
       
       ModuleStandardMenuManagement.addNode(\''.addslashes($newNodeId).'\', \''.addslashes($_POST['button_title']).'\', '.(int)$_POST['parent_id'].', '.(int)$visible.');
       
        ';
      //end js answer

    }

  }

  public function updatePage() {
    global $site;
    //make url
    if ($_POST['url'] == '') {
      if ($_POST['page_title'] != '') {
        $_POST['url'] = Db::makeUrl($_POST['page_title'], $_POST['page_id']);
      } else {
        $_POST['url'] = Db::makeUrl($_POST['button_title'], $_POST['page_id']);
      }
    } else {
      $tmpUrl = str_replace("/", "-", $_POST['url']);
      $i = 1;
      while (!Db::availableUrl($tmpUrl, $_POST['page_id'])) {
        $tmpUrl = $_POST['url'].'-'.$i;
        $i++;
      }
      $_POST['url'] = $tmpUrl;
    }
    //end make url

    if (strtotime($_POST['created_on']) === false) {
      $errorCreatedOn = true;
    } else {
      $errorCreatedOn = false;
    }

    if (strtotime($_POST['last_modified']) === false) {
      $errorLastModified = true;
    } else {
      $errorLastModified = false;
    }

    if ($_POST['type'] == 'redirect' && $_POST['redirect_url'] == '') {
      $errorEmptyRedirectUrl = true;
    } else {
      $errorEmptyRedirectUrl = false;
    }


    if ($errorCreatedOn || $errorLastModified || $errorEmptyRedirectUrl) {
      $answer = '';
      if($errorCreatedOn)
        $answer .= 'document.getElementById(\'property_created_on_error\').style.display = \'block\';';

      if($errorLastModified)
        $answer .= 'document.getElementById(\'property_last_modified_error\').style.display = \'block\';';

      if($errorEmptyRedirectUrl)
        $answer .= 'document.getElementById(\'property_type_error\').style.display = \'block\';';

      $answer .= 'document.getElementById(\'loading\').style.display = \'none\';';


      echo $answer;

    } else {
      //report about changed URL
      $page = \Modules\standard\content_management\DbFrontend::getElement($_POST['page_id']);
      if($page['url'] != $_POST['url']){
        $elementZone = $site->getZone($_POST['zone_name']);
        $element = $elementZone->getElement($_POST['page_id']);
        $oldUrl = $element->getLink();
      }
      //end report about changed URL

      Db::updateContentElement($_POST['page_id'], $_POST);

      if($page['url'] != $_POST['url']){
        $elementZone = $site->getZone($_POST['zone_name']);
        $element = $elementZone->getElement($_POST['page_id']);
        $newUrl = $element->getLink();
        $site->dispatchEvent('administrator', 'system', 'url_change', array('old_url'=>$oldUrl, 'new_url'=>$newUrl));
      }


      if (!$_POST['visible']) {
        $icon = 'node.ui.addClass(\'x-tree-node-disabled \');';
      } else {
        $icon = '';
      }

      echo '
      document.getElementById(\'loading\').style.display = \'none\';
       
      document.getElementById(\'property_last_modified_error\').style.display = \'none\';
      document.getElementById(\'property_created_on_error\').style.display = \'none\';      
      document.getElementById(\'property_type_error\').style.display = \'none\';       
       
       
      var form = document.getElementById(\'property_form\');
      form.property_url.value = \''.\Library\Php\Js\Functions::htmlToString($_POST['url']).'\';
       
       
      var node = iTree.getTree().getSelectionModel().getSelectedNode();
     	node.setText(\''.\Library\Php\Js\Functions::htmlToString($_POST['button_title']).'\');
      node.ui.removeClass(\'x-tree-node-disabled\');
        '.$icon.'
      ';    
    }




  }


  public function getPage() {
    global $site;
    //$site = new \Frontend\Site();
    //$site->configZones();
    if (isset($_REQUEST['id']) && isset($_REQUEST['zone_name'])) {
      $id = $_REQUEST['id'];
      $zoneName = $_REQUEST['zone_name'];
      $page = \Modules\standard\content_management\Db::menuElement($id);
      $pageElement = $site->getZone($zoneName)->getElement($page['id']);
      $tmpZone = $site->getZone($zoneName);
      $parentElement = $tmpZone->getElement($page['parent']);

      switch($page['type']) {
        case 'default':
        case 'inactive':
        case 'subpage':
        case 'redirect':
        //do nothing
          break;
        default:
          $page['type'] = 'default';
          break;
      }

      require_once(BASE_DIR.LIBRARY_DIR.'php/js/functions.php');
      $answer = '
      var form = document.getElementById(\'property_form\');
      form.action.value = \'update\';
      form.property_id.value = \''.\Library\Php\Js\Functions::htmlToString($page['id']).'\';
      form.property_button_title.value = \''.\Library\Php\Js\Functions::htmlToString($page['button_title']).'\';
      form.property_page_title.value = \''.\Library\Php\Js\Functions::htmlToString($page['page_title']).'\';
      form.property_keywords.value = \''.\Library\Php\Js\Functions::htmlToString($page['keywords']).'\';
      form.property_description.value = \''.\Library\Php\Js\Functions::htmlToString($page['description']).'\';
      form.property_url.value = \''.\Library\Php\Js\Functions::htmlToString($page['url']).'\';
      form.property_created_on.value = \''.\Library\Php\Js\Functions::htmlToString(substr($page['created_on'], 0, 10)).'\';
      form.property_last_modified.value = \''.\Library\Php\Js\Functions::htmlToString(substr($page['last_modified'], 0, 10)).'\';
      form.property_visible.checked = '.\Library\Php\Js\Functions::htmlToString(($page['visible'])? 'true' : 'false').';
      form.property_rss.checked = '.\Library\Php\Js\Functions::htmlToString(($page['rss'])? 'true' : 'false').';
      document.getElementById(\'property_type_'.$page['type'].'\').checked = true;
      form.property_redirect_url.value = \''.\Library\Php\Js\Functions::htmlToString($page['redirect_url']).'\';
      document.getElementById(\'url_prefix\').innerHTML = \''.\Library\Php\Js\Functions::htmlToString(substr($pageElement->getLink(), 0, strlen($pageElement->getLink()) - strlen($page['url']) -1 )).'\';
      document.getElementById(\'url_suffix\').innerHTML = \''.\Library\Php\Js\Functions::htmlToString($page['url']).'\';
      document.getElementById(\'content\').style.display = \'block\';
      
      document.getElementById(\'loading\').style.display = \'none\';

      document.getElementById(\'property_last_modified_error\').style.display = \'none\';
      document.getElementById(\'property_created_on_error\').style.display = \'none\';      
      document.getElementById(\'property_type_error\').style.display = \'none\';
      
      ';

      echo $answer;
    }
  }




}







