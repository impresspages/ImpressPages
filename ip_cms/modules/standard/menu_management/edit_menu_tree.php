<?php
/**
 * @package	ImpressPages
 * @copyright	Copyright (C) 2011 ImpressPages LTD.
 * @license	GNU/GPL, see ip_license.html
 */

namespace Modules\standard\menu_management;
if (!defined('BACKEND')) exit; 

require_once (__DIR__.'/db.php');


class EditMenuTree {
  var $languages;
  var $zones;
  var $currentLanguage;
  var $currentZone;
  var $currentMenuTitle;
  var $currentElement;
  var $mode;

  function __construct() {
    global $cms;
    global $site;

    $this->languages = Db::languages();
    if(sizeof($this->languages) > 0)
      $this->currentLanguage = reset($this->languages);
    $this->zones = Db::zones();
    $this->currentZone = reset($this->zones);


    if(isset($_SESSION['backend_modules']['standard']['menu_management']['menu_name'])) {
        if($site->getZone($_SESSION['backend_modules']['standard']['menu_management']['menu_name']['name'])) {
          $this->currentZone = $_SESSION['backend_modules']['standard']['menu_management']['menu_name'];
        }
    }

    if(isset($_SESSION['backend_modules']['standard']['menu_management']['language_id'])) {
      if($site->getLanguageById($_SESSION['backend_modules']['standard']['menu_management']['language_id'])) {
        $this->currentLanguage = $_SESSION['backend_modules']['standard']['menu_management']['language_id'];
      }
    }


    if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'navigate') {
      if(isset($_REQUEST['menu_name'])) {
        $this->currentZone = $this->zones[$_REQUEST['menu_name']];
        $_SESSION['backend_modules']['standard']['menu_management']['menu_name'] = $this->currentZone;
      }

      if(isset($_REQUEST['language_id'])) {
        $this->currentLanguage = $this->languages[$_REQUEST['language_id']];
        $_SESSION['backend_modules']['standard']['menu_management']['language_id'] = $this->currentLanguage;
      }
    }
    $this->mode = "default";
  }



  function manageMenu() {
    global $parametersMod;
    global $cms;

    if(sizeof($this->languages) == 0) {
      trigger_error("There are no languages. Please insert at least one language.");
      return;
    }


    if(sizeof($this->zones) > 0) {
      $this->currentElement = Db::rootContentElement($this->currentZone['id'], $this->currentLanguage['id']);
      if($this->currentElement == null) { /*try to create*/
        Db::createRootZoneElement($this->currentZone['id'], $this->currentLanguage['id']);
        $this->currentElement = Db::rootContentElement($this->currentZone['id'], $this->currentLanguage['id']);
        if($this->currentElement == null) {	/*fail to create*/
          trigger_error("Can't create root zone element.");
          return;
        }
      }
    }else {
      trigger_error("There is no assigned zones.");
      return;
    }

    $answer = '
               <div id="languages">'.$this->tepLanguages().'</div>
               <div id="menus">'.$this->tepMenus("men_menu", $this->currentZone['id'], $this->zones, 'id', 'translation').'</div>
	               '.$this->tepMenuTree().'
      ';

    $answer .= '
      <script type="text/javascript">
        function confirm_delete(action, question){
          var answer = confirm(question); 
          if (answer)
             document.location.href = action;
        }
        var translation_are_you_sure_you_wish_to_delete = \''.$parametersMod->getValue('standard', 'menu_management','admin_translations','question_delete').'\';      
        var translation_please_enter_title = \''.$parametersMod->getValue('standard', 'menu_management','admin_translations','enter_title').'\';      
        var translation_title = \''.$parametersMod->getValue('standard', 'menu_management','admin_translations','title').'\';
        
        var translation_new_page = \''.$parametersMod->getValue('standard', 'menu_management','admin_translations','new_page').'\';
        var translation_new_sub_page = \''.$parametersMod->getValue('standard', 'menu_management','admin_translations','new_subpage').'\';
        var translation_delete = \''.$parametersMod->getValue('standard', 'menu_management','admin_translations','delete').'\';
        var translation_edit_content = \''.$parametersMod->getValue('standard', 'menu_management','admin_translations','edit').'\';
              
      </script>
  
      ';

    $content_management_module = \Db::getModule(null, 'standard', 'content_management');
    $answer .= '<div style="display: none;" id="worker"><form action="'.$cms->generateWorkerUrl($content_management_module['id']).'" method="post" id="worker_form" target="worker_frame"></form></div>';
    $answer .= '<iframe style="width: 0px; height: 0px; border: 0;" onload="worker_frame_loaded()" name="worker_frame" width="10" height="10"></iframe>';
    $answer .= '<script type="text/javascript">
                      function worker_frame_loaded(){
                         var errors = Array();
                         var iFrameDocObj = window.frames[\'worker_frame\'].window.document;
                         
                         //alert(iFrameDocObj.body.innerHTML);
        variables = window.frames[\'worker_frame\'].variables;
        errors = window.frames[\'worker_frame\'].errors;
        notes = window.frames[\'worker_frame\'].notes;
        if(window.frames[\'worker_frame\'].script)
        eval(window.frames[\'worker_frame\'].script);
                        /*         eval(iFrameDocObj.body.innerHTML);
                         if (errors.length > 0){
                           alert(errors[0]);
                           window.location = window.location.href;                            
                         }*/
                      }
                  </script>';
    return $answer;
  }




  function tepLanguages() {
    global $cms;
    $answer = "
      <script type=\"text/javascript\">
        function language_redirect(language_id){
           document.location = '?module_id=".$cms->curModId."&action=navigate&language_id=' + language_id + '&menu_name=".$this->currentZone['name']."&security_token=".$cms->session->securityToken()."'; 
        }
      </script>
    ";
    foreach($this->languages as $key => $language) {
      if ($language['id'] == $this->currentLanguage['id'])
        $answer .= '<a class="selected" href="javascript:void(0);" onclick="language_redirect(\''.$language['id'].'\');">'.htmlspecialchars($language['d_short']).'</a>';
      else
        $answer .= '<a href="javascript:void(0);" onclick="language_redirect(\''.$language['id'].'\');">'.htmlspecialchars($language['d_short']).'</a>';
    }
    return $answer;
  }



  function tepMenus() {
    global $cms;
    $answer = "
      <script>
        function menu_redirect(menu_name){
           document.location = '?module_id=".$cms->curModId."&action=navigate&language_id=".$this->currentLanguage['id']."&menu_name=' + menu_name + '&security_token=".$cms->session->securityToken()."'; 
        }
      </script>
    ";
    $answer .= '<form><div><select onChange="menu_redirect(this.value)">';
    foreach($this->zones as $key => $zone) {
      if ($zone['name'] == $this->currentZone['name'])
        $answer .= '<option selected value="'.htmlspecialchars($zone['name']).'">'.htmlspecialchars($zone['translation']).'</option>';
      else
        $answer .= '<option value="'.htmlspecialchars($zone['name']).'">'.htmlspecialchars($zone['translation']).'</option>';
    }
    $answer .= '</select></div></form>';
    return $answer;
  }



  function tepMenuElement($element, $level = 1) {
    global $cms;
    $answer = '';
    $children = Db::contentElementChildren($element);
    foreach($children as $key => $lock) {
      if($answer != '')
        $answer .= ", ";

      $children = $this->tepMenuElement($lock['id'], $level + 1);

      if ($lock['visible'])
        $disabled = "false";
      else
        $disabled = "true";

      //if($children != "")
      /*if($level >= $this->currentZone['depth'])
          $allowDrop = 'false';
        else*/
      $allowDrop = 'true';
      $answer .=  '{allowDrop: '.$allowDrop.', disabled: '.$disabled.', "expanded" : true, "text" : "'.htmlspecialchars(addslashes($lock['button_title'])).'", "id" : "'.$lock['id'].'", "leaf" : false, "cls" : "folder", "children": ['.$children.']}';
      //else
      //  $answer .=  '{disabled: '.$disabled.', "expanded" : true, "text" : "'.$lock['title'].'", "id" : "'.$lock['id'].'", "leaf" : true, "cls" : "file"}';

    }
    return $answer;
  }

  function tepMenuTree() {
    global $cms;
    global $parametersMod;
    global $site;

    $associatedZones = explode("\n", $parametersMod->getValue('standard', 'menu_management', 'options', 'auto_rss_zones'));
    $autoRss = 'false';
    foreach($associatedZones as $key => $value) {
      if($value == $this->currentZone['name'])
        $autoRss = 'true';
    }

    $hideNewPages = 'false';

    if ($parametersMod->getValue('standard', 'menu_management', 'options', 'hide_new_pages')) {
      $hideNewPages = 'true';
    }

    switch($this->mode) {
      case "default":
        $answer = '';

        //ext


        $answer .= '
  <link rel="stylesheet" type="text/css" href="'.BASE_URL.LIBRARY_DIR.'js/ext/resources/css/ext-all.css" />

  <!-- GC --> <!-- LIBS -->     <script type="text/javascript" src="'.BASE_URL.LIBRARY_DIR.'js/ext/adapter/yui/yui-utilities.js"></script>
  <script type="text/javascript" src="'.BASE_URL.LIBRARY_DIR.'js/ext/adapter/yui/ext-yui-adapter.js"></script>     <!-- ENDLIBS -->
  <script type="text/javascript" src="'.BASE_URL.LIBRARY_DIR.'js/ext/ext-all.js"></script>

  <!-- Common Styles for the examples -->
  <link rel="stylesheet" type="text/css" href="'.BASE_URL.MODULE_DIR.'/standard/menu_management/design/edit_menu_tree.css" />



  <script type="text/javascript">
  var json = ['.$this->tepMenuElement($this->currentElement).'];


  var root_id = '.$this->currentElement.';

  var current_menu_title = "";

  var current_menu_auto_rss = '.$autoRss.';
  var current_menu_hide_new_pages = '.$hideNewPages.';


  var iTree;

  var cmsLink = \''.addslashes($cms->generateWorkerUrl()).'\';
  var zoneName = \''.addslashes($this->currentZone['name']).'\';
  var zoneLink = \''.$site->generateUrl($this->currentLanguage['id'], $this->currentZone['name']).'\';
  </script>


  <script type="text/javascript">
  var cms_worker = "'.BACKEND_WORKER_FILE.'"; //CMS file, that loads db class and give management to module/admin_worker.php if session exists".
  </script>
  <script type="text/javascript" src="'.BASE_URL.MODULE_DIR.'standard/menu_management/edit_menu_tree.js"></script>
  <div id="tree-div" >
                            <div class="button"><img src="'.BASE_URL.MODULE_DIR.'/standard/menu_management/design/icon_add_tr.gif" /><a onclick="iTree.getTree().root.select(); iTree.newSubNode(\''.$parametersMod->getValue('standard', 'menu_management', 'admin_translations', "untitled").'\')" href="javascript:void(0);">'.$parametersMod->getValue('standard', 'menu_management', 'admin_translations', "new_page").'</a></div>

  </div>

  ';




        //eof ext







        break;

    }
    return $answer;
  }

  function getCurrentLanguage() {
    return $this->currentLanguage;
  }
  function getCurrentZone() {
    return $this->currentZone;
  }
  function setCurrentLanguage($currentLanguage) {
    $this->currentLanguage = $currentLanguage;
  }

  function setCurrentZone($current_menu) {
    $this->currentZone = $current_menu;
  }

}

