<?php
/**
 * @package		ImpressPages
 * @copyright	Copyright (C) 2009 JSC Apro media.
 * @license		GNU/GPL, see ip_license.html
 */

namespace update_1_0_0_alpha_to_1_0_1_beta;

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
    $this->deleteFolders[] = 'backend';
    $this->deleteFolders[] = 'frontend';
    $this->deleteFolders[] = 'includes';
    $this->deleteFolders[] = 'install';
    $this->deleteFolders[] = 'library';
    $this->deleteFolders[] = 'modules';

    $this->deleteFiles = array();
    $this->deleteFiles[] = 'admin.php';
    $this->deleteFiles[] = 'backend_frames.php';
    $this->deleteFiles[] = 'backend_worker.php';
    $this->deleteFiles[] = 'cron.php';
    $this->deleteFiles[] = 'index.php';
    $this->deleteFiles[] = 'license.html';
    $this->deleteFiles[] = 'sitemap.php';

    $this->addFolders = array();
    $this->addFolders[] = 'audio';
    $this->addFolders[] = 'ip_cms';
    $this->addFolders[] = 'ip_configs';
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
    return 8;
  }

  public function process () {
    global $htmlOutput;
    global $navigation;    
    
    $answer = '';
    

    switch ($this->curAction) {
      default:
      case 1:
        $answer .= $this->updateConfig();
      break;
      case 2:
        $answer .= $this->cancelWriteConfig();
      break;
      case 3:
        $answer .= $this->filesToDelete();
      break;
      case 4:
        $answer .= $this->filesToUpload();
      break;
      case 5:
        $answer .= $this->updateThemes();
      break;
      case 6:
        $answer .= $this->cancelWriteThemes();
      break;
      case 7:
        $answer .= $this->writeableAudioDir();
      break;
      case 8:
        $answer .= $this->updateDatabase();
      break;
    }
    
    
    return $answer;
  }
  
  public function updateConfig () {
    global $htmlOutput;
    global $navigation;
    
    $answer = '';
    if (file_exists('../ip_config.php') && is_writeable('../ip_config.php')) {
      $config = file_get_contents('../ip_config.php');
      if (strpos($config, 'AUDIO_DIR') === false) { //check if wee have not updated the file yet.
        $audioConfigs = "define('AUDIO_DIR', 'audio/'); //uploaded audio directory
	  define('TMP_AUDIO_DIR', 'audio/tmp/'); //temporary audio directory
	  define('AUDIO_REPOSITORY_DIR', 'audio/repository/'); //audio repository. Used for TinyMCE and others where user can browse the files.
        
    ";
        $config = str_replace("define('ERRORS_SHOW'", $audioConfigs."define('ERRORS_SHOW'", $config);      
        
        $config = str_replace('includes/', 'ip_cms/includes/', $config);
        $config = str_replace('backend/', 'ip_cms/backend/', $config);
        $config = str_replace('frontend/', 'ip_cms/frontend/', $config);
        $config = str_replace('library/', 'ip_libs/', $config);
        $config = str_replace('modules/', 'ip_cms/modules/', $config);
        
        
        $config = str_replace('TEMPLATE_DIR', 'THEME_DIR', $config);
        $config = str_replace('TEMPLATE', 'THEME', $config);
        
        
        $newConstants = "define('CONFIG_DIR', 'ip_configs/'); //modules configuration directory
	  define('PLUGIN_DIR', 'ip_plugins/'); //plugins directory
    ";
        
        
        $config = str_replace("define('THEME_DIR'", $newConstants."define('THEME_DIR'", $config);      
        
        $config = str_replace('backend_worker.php', 'ip_backend_worker.php', $config);
  
        file_put_contents('../ip_config.php', $config);
      }
      
      header("location: ".$navigation->generateLink($navigation->curStep(), $navigation->curScript(), $navigation->curAction() + 1));
    } else {
      $answer .= MOVE_AND_MAKE_CONFIG_WRITEABLE;
      
      $answer .= '<br/>';
      $answer .= '<br/>';
      $answer .= '<br/>';
      $answer .= '<br/>';
      $answer .= '<br/>';
      
      $answer .= $htmlOutput->button(IP_NEXT, $navigation->generateLink($navigation->curStep(), $navigation->curScript(), $navigation->curAction()));    
    }
    
    return $answer;
  }
  
  public function cancelWriteConfig(){
    global $htmlOutput;
    global $navigation;
    $answer = '';
  
    if (is_writeable('../ip_config.php')) {
      $answer .= CANCEL_CONFIG_WRITEABLE;
      $answer .= $htmlOutput->button(IP_NEXT, $navigation->generateLink($navigation->curStep(), $navigation->curScript(), $navigation->curAction()));    
    } else {
      header("location: ".$navigation->generateLink($navigation->curStep(), $navigation->curScript(), $navigation->curAction() + 1));
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
    
    if($this->curStep <= $this->stepCount) {
        if (is_dir('../audio/') ) {
          return false;
        } else {
          return true;
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
    $tableFiles = array();

    if ($this->curStep <= $this->stepCount) {
          $tableFolders[] = '/audio/';
          $tableFolders[] = '';
    } else {
      foreach ($this->addFolders as $folder){
        if (!is_dir('../'.$folder) ) {
          $tableFolders[] = '/'.$folder.'/';
          $tableFolders[] = '';
        }
      }

      foreach ($this->addFiles as $file){
        if (!is_file('../'.$file) ) {
          $tableFiles[] = '/'.$file;
          $tableFiles[] = '';
        }
      }
    }

    if (sizeof($tableFolders)) {
      $answer .= UPLOAD_DIRECTORIES.$htmlOutput->table($tableFolders);
      $answer .= '<br/>';
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
  
  public function updateThemes() {
    global $navigation;
    global $htmlOutput;
    
    $answer = '';
      
    if ($this->directoryIsWriteable('../'.THEME_DIR)) {
      $phpFiles = $this->getPhpFiles('../'.THEME_DIR);
      foreach ($phpFiles as $key => $file) {
        $this->prepareTemplate($file);
      }
      header("location: ".$navigation->generateLink($navigation->curStep(), $navigation->curScript(), $navigation->curAction() + 1));
    } else {
      $answer .= MAKE_TEMPLATE_WRITEABLE;
      $answer .= "<br/>";
      $answer .= "<br/>";
      $answer .= "<br/>";
      $answer .= "<br/>";
      $answer .= "<br/>";
      $answer .= $htmlOutput->button(IP_NEXT, $navigation->generateLink($navigation->curStep(), $navigation->curScript(), $navigation->curAction()));
    }

    return $answer;   
  }

  public function cancelWriteThemes () {
    global $navigation;
    global $htmlOutput;
    
    $answer = '';
      
    if (is_writeable ('../'.THEME_DIR)) {
      $answer .= CANCEL_TEMPLATE_WRITEABLE;
      $answer .= "<br/>";
      $answer .= "<br/>";
      $answer .= "<br/>";
      $answer .= "<br/>";
      $answer .= "<br/>";
      $answer .= $htmlOutput->button(IP_NEXT, $navigation->generateLink($navigation->curStep(), $navigation->curScript(), $navigation->curAction()));
    } else {
      header("location: ".$navigation->generateLink($navigation->curStep(), $navigation->curScript(), $navigation->curAction() + 1));
    }
    
    return $answer;   
  }
  
  
  public function prepareTemplate ($file) {
    $template = file_get_contents($file);
    
    $template = str_replace('TEMPLATE_DIR', 'THEME_DIR', $template);
    $template = str_replace('TEMPLATE', 'THEME', $template);

    file_put_contents($file, $template);    
  }
  
  
  public function writeableAudioDir () {
    global $navigation;
    global $htmlOutput;
    
    $answer = '';
      
    if (!$this->directoryIsWriteable ('../'.AUDIO_DIR)) {
      $answer .= MAKE_AUDIO_WRITEABLE;
      $answer .= "<br/>";
      $answer .= "<br/>";
      $answer .= "<br/>";
      $answer .= "<br/>";
      $answer .= "<br/>";
      $answer .= $htmlOutput->button(IP_NEXT, $navigation->generateLink($navigation->curStep(), $navigation->curScript(), $navigation->curAction()));
    } else {
      header("location: ".$navigation->generateLink($navigation->curStep(), $navigation->curScript(), $navigation->curAction() + 1));
    }
    
    return $answer;   
  }
  
  public function updateDatabase() {
    global $navigation;
    global $htmlOutput;
    
    require_once('db/db100.php');
    
    $answer = '';
    if (\Db_100::getSystemVariable('version') != '1.0.1 Beta') {
      $module = \Db_100::getModule(null, 'standard', 'menu_management');
      $group = \Db_100::getParameterGroup($module['id'], 'admin_translations');
      
      $sql = "
      INSERT INTO `".DB_PREF."parameter` (`name`, `admin`, `row_number`, `regexpression`, `group_id`, `translation`, `comment`, `type`) VALUES
('error_type_url_empty', 1, 17, '', ".$group['id'].", 'Error type url empty', NULL, 'string');
      ";
      $rs = mysql_query($sql);
      if ($rs) {
        $sql2 = "INSERT INTO `".DB_PREF."par_string` (`value`, `parameter_id`) VALUES
('External url can\'t be empty', ".mysql_insert_id().")";
        $rs2 = mysql_query($sql2);
        if (!$rs2)
          trigger_error($sql2." ".mysql_error());
      } else {
        trigger_error($sql." ".mysql_error());
      }
        
      if ($this->curStep == $this->stepCount)
        \Db_100::setSystemVariable('version','1.0.1 Beta');

    } 
    
    if ($this->curStep == $this->stepCount) {
      header("location: ".$navigation->generateLink($navigation->curStep() + 1));
    } else {
      header("location: ".$navigation->generateLink($navigation->curStep(), $navigation->curScript() + 1));
    }
    
    return $answer;
  }
  

  function directoryIsWriteable($dir){
  	$answer = true;
  	if(!is_writable($dir)){
  		$answer = false;
  	}
  		
  	if ($handle = opendir($dir)) { 
  	    while (false !== ($file = readdir($handle))) {
  				if($file != ".." && !is_writable($dir.'/'.$file)){
  					$answer = false;				
  				}
  	    }
  	    closedir($handle);
  	} 
  
  	return $answer;
  }
  
  
  function getPhpFiles($dir) {
  	$answer = array();
  	if ($handle = opendir($dir)) { 
	    while (false !== ($file = readdir($handle))) {
	      if(is_dir($dir.$file) && $file != ".." && $file != ".") {
	        $answer = array_merge($answer, $this->getPhpFiles($dir.$file));
	      } else {
  				if($file != ".." && $file != "."){
  				  if (strtolower(substr($file, -4)) == '.php')
              $answer[] = $dir.'/'.$file;
  				}
	      }
	    }
	    closedir($handle);
  	} 
  
  	return $answer;
  }
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
}



