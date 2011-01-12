<?php
/**
 * @package   ImpressPages
 * @copyright Copyright (C) 2011 ImpressPages LTD.
 * @license   GNU/GPL, see ip_license.html
 */
namespace update_1_0_3_beta_to_1_0_4;

if (!defined('CMS')) exit;

require_once('translations.php');

class Script {
  var $deleteFiles;
  var $addFiles;
  var $deleteFolders;
  var $addFolders;
  
  var $stepCount;
  var $curStep;
  var $curAction;
   
  
  public function __construct($stepCount, $curStep, $curAction) {
    $this->deleteFolders = array();
    $this->deleteFolders[] = 'ip_cms';
    $this->deleteFolders[] = 'ip_libs';

    $this->deleteFiles = array();
    $this->deleteFiles[] = 'admin.php';
    $this->deleteFiles[] = 'index.php';
    $this->deleteFiles[] = 'ip_backend_frames.php';
    $this->deleteFiles[] = 'ip_backend_worker.php';
    $this->deleteFiles[] = 'ip_cron.php';
    $this->deleteFiles[] = 'ip_license.html';
    $this->deleteFiles[] = 'sitemap.php';

    $this->addFolders = array();
    $this->addFolders[] = 'ip_cms';
    $this->addFolders[] = 'ip_libs';
    $this->addFolders[] = 'ip_plugins';
    
    $this->addFiles = array();
    $this->addFiles[] = 'admin.php';
    $this->addFiles[] = 'index.php';
    $this->addFiles[] = 'ip_backend_frames.php';
    $this->addFiles[] = 'ip_backend_worker.php';
    $this->addFiles[] = 'ip_cron.php';
    $this->addFiles[] = 'ip_license.html';
    $this->addFiles[] = 'sitemap.php';

    $this->stepCount = $stepCount;
    $this->curStep = $curStep;
    $this->curAction = $curAction;
  }
  
  public function getActionsCount() {
    return 3;
  }

  public function process () {
    global $htmlOutput;
    global $navigation;    
    
    $answer = '';
    

    switch ($this->curAction) {
      default:
      case 1:
        $answer .= $this->filesToDelete();
      break;
      case 2:
        $answer .= $this->filesToUpload();
      break;
      case 3:
        $answer .= $this->updateDatabase();
      break;
    }
    
    
    return $answer;
  }


  
  public function needToDelete() {
    $answer = false;
    if($this->curStep == 1 && !isset($_SESSION['process'][1]['deleted'])) {
      foreach ($this->deleteFolders as $folder){
        if (is_dir('../'.$folder) ) {
          $answer = true;
        }
      }
      foreach ($this->deleteFiles as $file){
        if (is_file('../'.$file) ) {
          $answer = true;
        }
      }
            
      if ($answer == false) {
        $_SESSION['process'][1]['deleted'] = true;
      }
    }
    return $answer;
  }
  
  public function needToUpload() {
    $answer = false;
    if($this->curStep == $this->stepCount && !isset($_SESSION['process'][1]['uploaded'])) {
      foreach ($this->addFolders as $folder){
        if (!is_dir('../'.$folder) ) {
          $answer = true;
        }
      }
      foreach ($this->addFiles as $file){
        if (!is_file('../'.$file) ) {
          $answer = true;
        }
      }
            
      if ($answer == false) {
        $_SESSION['process'][1]['uploaded'] = true;
      }
    }
    return $answer;
  }  
  
  public function filesToDelete() {
    global $navigation;
    global $htmlOutput;

    $answer = '';
    
    $tableFolders = array();
    
    foreach ($this->deleteFolders as $folder){
      if (is_dir('../'.$folder) ) {
        $tableFolders[] = '/'.$folder.'/';
        $tableFolders[] = '';
      }
    }
    
    
    if (sizeof($tableFolders)) {
      $answer .= REMOVE_DIRECTORIES.$htmlOutput->table($tableFolders);
      $answer .= '<br/>';
    }
    
    
    
    $tableFiles = array();
    foreach ($this->deleteFiles as $file){
      if (is_file('../'.$file) ) {
        $tableFiles[] = '/'.$file;
        $tableFiles[] = '';
      }
    }
    
    if (sizeof($tableFiles)) {
      $answer .= REMOVE_FILES.$htmlOutput->table($tableFiles);
    }
    
    if ($this->needToDelete())
      $answer .= $htmlOutput->button(IP_NEXT, $navigation->generateLink($navigation->curStep(), $navigation->curScript(), $navigation->curAction()));
    else {
      header("location: ".$navigation->generateLink($navigation->curStep(), $navigation->curScript(), $navigation->curAction() + 1));
    }    

    return $answer;
  }
  
  public function filesToUpload(){
    global $navigation;
    global $htmlOutput;
    
    $answer = '';
    
    $tableFolders = array();
    
    foreach ($this->addFolders as $folder){
      if (!is_dir('../'.$folder) ) {
        $tableFolders[] = '/'.$folder.'/';
        $tableFolders[] = '';
      }
    }
    
    
    if (sizeof($tableFolders)) {
      $answer .= UPLOAD_DIRECTORIES.$htmlOutput->table($tableFolders);
      $answer .= '<br/>';
    }
    
    
    
    $tableFiles = array();
    foreach ($this->addFiles as $file){
      if (!is_file('../'.$file) ) {
        $tableFiles[] = '/'.$file;
        $tableFiles[] = '';
      }
    }
    
    if (sizeof($tableFiles)) {
      $answer .= UPLOAD_FILES.$htmlOutput->table($tableFiles);
    }

    if ($this->needToUpload())
      $answer .= $htmlOutput->button(IP_NEXT, $navigation->generateLink($navigation->curStep(), $navigation->curScript(), $navigation->curAction()));
    else {
      header("location: ".$navigation->generateLink($navigation->curStep(), $navigation->curScript(), $navigation->curAction() + 1));
    }    
    return $answer;
  }
  

  

  
  public function updateDatabase() {
    global $navigation;
    global $htmlOutput;
    require_once('db/db100.php');
    
    $answer = '';
    if (\Db_100::getSystemVariable('version') != '1.0.4') {

      
      $module = \Db_100::getModule(null, 'administrator', 'email_queue');
      $group = \Db_100::getParameterGroup($module['id'], 'admin_translations'); 
      \Db_100::addStringParameter($group['id'], 'Email queue', 'email_queue', 'Email queue', 1);      
      
      $module = \Db_100::getModule(null, 'developer', 'modules');
      $group = \Db_100::getParameterGroup($module['id'], 'admin_translations_install'); 
      \Db_100::addStringParameter($group['id'], 'Updated module detected', 'updated_module_detected', 'New version detected', 1);      
      \Db_100::addStringParameter($group['id'], 'Install', 'install', 'Install', 1);      
      \Db_100::addStringParameter($group['id'], 'Update', 'update', 'Update', 1);      
      

      $module = \Db_100::getModule(null, 'standard', 'menu_management');
      $group = \Db_100::getParameterGroup($module['id'], 'admin_translations'); 
      \Db_100::addStringParameter($group['id'], 'Save', 'save', 'Save', 1);      
      

      $module = \Db_100::getModule(null, 'community', 'newsletter');
      $group = \Db_100::getParameterGroup($module['id'], 'admin_translations'); 
      \Db_100::addStringParameter($group['id'], 'Where to send?', 'where_to_send', 'Send test email to: ', 1);      
      
      $sql = "update `".DB_PREF."parameter` set `name` = 'was_sent' where `name` = 'was_send' ";
      $rs = mysql_query($sql);
      if(!$rs){
        trigger_error($sql.' '.mysql_error());
      }

      //delete duplicated parameter
      $module = \Db_100::getModule(null, 'developer', 'std_mod');
      $group = \Db_100::getParameterGroup($module['id'], 'admin_translations'); 
      $sql = "select * from `".DB_PREF."parameter` where `group_id` = '".((int)$group['id'])."' and `name` = '".mysql_real_escape_string('error_required')."'";
      $rs = mysql_query($sql);
      $parameters = array();
      if($rs){
        while($lock = mysql_fetch_assoc($rs)){
          $parameters[] = $lock;
        }
      } else {
        trigger_error($sql." ".mysql_error());
      }
      if(sizeof($parameters) > 1){
        $sql = "delete from `".DB_PREF."parameter` where `id` = ".((int)$parameters[1]['id'])." ";
        $rs = mysql_query($sql);
        if(!$rs){
          trigger_error($sql." ".mysql_error());
        }
        
      }
      //end delete duplicated parameter

      //fix logo galery layout field
      $sql = "update `".DB_PREF."mc_text_photos_logo_gallery` set `layout` = 'default' where `layout` = 'undefined' ";
      $rs = mysql_query($sql);
      if(!$rs){
        trigger_error($sql.' '.mysql_error());
      }
      
      
      //correct separator module_id field
      $sql = "select * from `".DB_PREF."content_element_to_modules` where `group_key` = 'text_photos' and `module_key` = 'separator' ";
      $rs = mysql_query($sql);
      if(!$rs){
        trigger_error($sql.' '.mysql_error());
      }
      $contentToModule = array();
      while($lock = mysql_fetch_assoc($rs)){
        $contentToModule[] = $lock;
      }
      
      $sql = "select * from `".DB_PREF."mc_text_photos_separator` where 1 ";
      $rs = mysql_query($sql);
      if(!$rs){
        trigger_error($sql.' '.mysql_error());
      }
      $separators = array();
      while($lock = mysql_fetch_assoc($rs)){
        $separators[] = $lock;
      }
      foreach($contentToModule as $key => $module){
        if(isset($separators[$key])){
          $sql = "update `".DB_PREF."content_element_to_modules` set `module_id` = ".(int)$separators[$key]['id']." where `id` = ".(int)$module['id']." ";
          $rs = mysql_query($sql);
          if(!$rs){
            trigger_error($sql.' '.mysql_error());
          }
          
        }
      }
      
      //end correct separator module_id  field
      
      
      //fix to_big to too_big
      $sql = "update `".DB_PREF."parameter` set `name` = 'too_big' where `name` = 'to_big' ";
      $rs = mysql_query($sql);
      if(!$rs){
        trigger_error($sql.' '.mysql_error());
      }
            
      
      if ($this->curStep == $this->stepCount){
        \Db_100::setSystemVariable('version','1.0.4');
      }      
    }
    
    if ($this->curStep == $this->stepCount) {
      header("location: ".$navigation->generateLink($navigation->curStep() + 1));
    } else {
      header("location: ".$navigation->generateLink($navigation->curStep(), $navigation->curScript() + 1));
    }
      
    return $answer;
  }
  



  
    
  

}

