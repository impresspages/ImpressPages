<?php
/**
 * @package   ImpressPages
 * @copyright Copyright (C) 2009 JSC Apro media.
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
      
      $newsletterModule = \Db_100::getModule(null, 'community', 'newsletter');
      
      
      //community/user module
      $sql = "select max(`row_number`) as 'max_row_number' from `".DB_PREF."module` where `group_id` = '".(int)$newsletterModule['group_id']."' ";
      $rs = mysql_query($sql);
      if($rs){
        if($lock = mysql_fetch_assoc($rs)){
          $maxRowNumber = $lock['max_row_number'];
        } else {
          trigger_error('Can\'t get max row number '.$sql);
        }
        
        $sqlUser = "
          INSERT INTO `".DB_PREF."module` (`group_id`, `row_number`, `name`, `admin`, `translation`, `managed`, `version`, `core`) 
          VALUES (".(int)$newsletterModule['group_id'].", ".($maxRowNumber+1).", 'user', 1, 'User', 1, '1.00', 1);
        ";
        
        $rs = mysql_query($sqlUser);
        if(!$rs){
          trigger_error($sqlUser.' '.mysql_error());
        }
        
      } else {
        trigger_error($sql.' '.mysql_error());
      }
      
      $sql = "
      CREATE TABLE IF NOT EXISTS `".DB_PREF."m_community_user` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `login` varchar(255) NOT NULL,
        `language_id` int(11) NOT NULL,
        `email` varchar(255) NOT NULL,
        `password` varchar(32) NOT NULL,
        `verified` tinyint(1) NOT NULL DEFAULT '0',
        `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `last_login` timestamp NULL DEFAULT NULL,
        `verification_code` varchar(32) NOT NULL,
        `new_email` varchar(255) DEFAULT NULL,
        `new_password` varchar(32) DEFAULT NULL,
        `warned_on` timestamp NULL DEFAULT NULL,
        `valid_until` timestamp NULL DEFAULT NULL COMMENT 'required for maintenance. Real date should be calculated in real time by last_login field.',
        PRIMARY KEY (`id`)
      ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;
      ";

      $rs = mysql_query($sql);
      if(!$rs){
        trigger_error($sql.' '.mysql_error());
      }
      
      
      //end community/user module
      
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

