<?php
/**
 * @package   ImpressPages
 * @copyright Copyright (C) 2009 JSC Apro media.
 * @license   GNU/GPL, see ip_license.html
 */
namespace update_1_0_4_to_1_0_5;

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
    require_once(__DIR__.'/parameter_manager.php');
    require_once (__DIR__.'/parameter_db.php');
         
    $answer = '';
    if (\Db_100::getSystemVariable('version') != '1.0.5') {
      
      $module = \Modules\developer\localization\Db::getModule('community', 'newsletter');
      
      
      //community/user module
      $sql = "select max(`row_number`) as 'max_row_number' from `".DB_PREF."module` where `group_id` = '".(int)$module['group_id']."' ";
      $rs = mysql_query($sql);
      if($rs){
        if($lock = mysql_fetch_assoc($rs)){
          $maxRowNumber = $lock['max_row_number'];
        } else {
          trigger_error('Can\'t get max row number '.$sql);
        }
        
        $sqlUser = "
          INSERT INTO `".DB_PREF."module` (`group_id`, `row_number`, `name`, `admin`, `translation`, `managed`, `version`, `core`) 
          VALUES (".(int)$module['group_id'].", ".($maxRowNumber+1).", 'user', 1, 'User', 1, '1.00', 1);
        ";

        $rs = mysql_query($sqlUser);
        if($rs){
          $userModuleId = mysql_insert_id();
                                echo 'Id '.$userModuleId;
          $users = $this->getUsers();
          foreach($users as $user){
            $this->addPermissions($userModuleId, $user['id']);
          }
        } else {
          trigger_error($sqlUser.' '.mysql_error());
        }

        
      } else {
        trigger_error($sql.' '.mysql_error());
      }
      
      $sql = "
      CREATE TABLE IF NOT EXISTS `".DB_PREF."m_community_user` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `login` varchar(255) NULL,
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
      

      \Modules\developer\localization\Manager::saveParameters(__DIR__.'/community_user_parameters.php');

      \Modules\developer\localization\Manager::saveParameters(__DIR__.'/standard_languages_parameters.php');
      //end community/user module
      


      //drop additional field in widget tables
      $tablesWithBaseUrl = array();
      $tablesWithBaseUrl[] = DB_PREF.'mc_text_photos_text_title';
      $tablesWithBaseUrl[] = DB_PREF.'mc_text_photos_text_photo';
      $tablesWithBaseUrl[] = DB_PREF.'mc_text_photos_text';
      $tablesWithBaseUrl[] = DB_PREF.'mc_text_photos_faq';
      $tablesWithBaseUrl[] = DB_PREF.'mc_misc_rich_text';
      
      foreach($tablesWithBaseUrl as $table){
        $sqlColumns = "show columns from `".$table."`";
        $rsColumns = mysql_query($sqlColumns);
        if($rsColumns){
          $columns = array();
          while($lockColumn = mysql_fetch_assoc($rsColumns)){
            $columns[$lockColumn['Field']] = 1;
          }       
          if(isset($columns['base_url'])){
            $sql = " alter table `".$table."` drop `base_url` ";
            $rs = mysql_query($sql);
            if(!$rs){
              trigger_error($sql.' '.mysql_error());
            }
          }
        } else {
          trigger_error($sql.' '.mysql_error());
        }
      }


      $replaceParameters = array();
      $replaceParameters[] = array('from' => 'show_unsubscribtion_button', 'to' => 'show_unsubscribe_button');
      $replaceParameters[] = array('from' => 'man_aditional_add_rss', 'to' => 'man_additional_add_rss');
      $replaceParameters[] = array('from' => 'man_aditional_button_title', 'to' => 'man_additional_button_title');
      $replaceParameters[] = array('from' => 'man_aditional_created_on', 'to' => 'man_additional_created_on');
      $replaceParameters[] = array('from' => 'man_aditional_description', 'to' => 'man_additional_description');
      $replaceParameters[] = array('from' => 'man_aditional_error_date_format', 'to' => 'man_additional_error_date_format');
      $replaceParameters[] = array('from' => 'man_aditional_inactive', 'to' => 'man_additional_inactive');
      $replaceParameters[] = array('from' => 'man_aditional_info', 'to' => 'man_additional_info');
      $replaceParameters[] = array('from' => 'man_aditional_keywords', 'to' => 'man_additional_keywords');
      $replaceParameters[] = array('from' => 'man_aditional_no_redirect', 'to' => 'man_additional_no_redirect');
      $replaceParameters[] = array('from' => 'man_aditional_page_properties', 'to' => 'man_additional_page_properties');
      $replaceParameters[] = array('from' => 'man_aditional_page_title', 'to' => 'man_additional_page_title');
      $replaceParameters[] = array('from' => 'man_aditional_redirect', 'to' => 'man_additional_redirect');
      $replaceParameters[] = array('from' => 'man_aditional_subpage', 'to' => 'man_additional_subpage');
      $replaceParameters[] = array('from' => 'man_aditional_type', 'to' => 'man_additional_type');
      $replaceParameters[] = array('from' => 'man_aditional_url', 'to' => 'man_additional_url');
      $replaceParameters[] = array('from' => 'man_aditional_visible', 'to' => 'man_additional_visible');


      foreach($replaceParameters as $parameter){
        $sql = " update `".DB_PREF."parameter` set `name` = '".mysql_real_escape_string($parameter['to'])."' where `name` = '".mysql_real_escape_string($parameter['from'])."' ";
        $rs = mysql_query($sql);
        if(!$rs){
          trigger_error($sql.' '.mysql_error());
        }
      }

      $replaceParameters = array();
      $replaceParameters[] = array('from' => 'Log zise in days', 'to' => 'Log size in days');
      $replaceParameters[] = array('from' => 'Field fonfiguration file', 'to' => 'Field configuration file');
      $replaceParameters[] = array('from' => 'Unpossible to delete the record', 'to' => 'Impossible to delete the record');
      $replaceParameters[] = array('from' => 'Aditional info', 'to' => 'Additional info');


      foreach($replaceParameters as $parameter){
        $sql = " update `".DB_PREF."parameter` set `translation` = '".mysql_real_escape_string($parameter['to'])."' where `translation` = '".mysql_real_escape_string($parameter['from'])."' ";
        $rs = mysql_query($sql);
        if(!$rs){
          trigger_error($sql.' '.mysql_error());
        }
      }



      if ($this->curStep == $this->stepCount){
        \Db_100::setSystemVariable('version','1.0.5');
      }      
    }
    
    if ($this->curStep == $this->stepCount) {
      header("location: ".$navigation->generateLink($navigation->curStep() + 1));
    } else {
      header("location: ".$navigation->generateLink($navigation->curStep(), $navigation->curScript() + 1));
    }
      
    return $answer;
  }
  



  private function getUsers(){
    $answer = array();
    $sql = "select * from `".DB_PREF."user` where 1";
    $rs = mysql_query($sql);
    if($rs){
      while($lock = mysql_fetch_assoc($rs)){
        $answer[] = $lock;
      }
      return $answer;
    } else {
      trigger_error($sql." ".mysql_error());
      return false;
    }

  }


  private function addPermissions($moduleId, $userId){
    $sql = "insert into `".DB_PREF."user_to_mod`
    set
    module_id = '".(int)$moduleId."',
    user_id = '".(int)$userId."'

    ";
    $rs = mysql_query($sql);
    if($rs){
      return mysql_insert_id();
    } else {
      trigger_error($sql." ".mysql_error());
      return false;
    }
  }
  
    
  

}

