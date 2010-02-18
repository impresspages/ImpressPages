<?php
/**
 * @package   ImpressPages
 * @copyright Copyright (C) 2009 JSC Apro media.
 * @license   GNU/GPL, see ip_license.html
 */
namespace update_1_0_2_beta_to_1_0_3_beta;

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
    return 5;
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
        $answer .= $this->updateThemes();
      break;
      case 4:
        $answer .= $this->cancelWriteThemes();
      break;      
      case 5:
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
  

  
  public function updateThemes() {
    global $navigation;
    global $htmlOutput;
    
    $answer = '';
      
    if ($this->directoryIsWriteable('../'.THEME_DIR)) {
      $phpFiles = $this->getFiles('../'.THEME_DIR, 'php');
      foreach ($phpFiles as $key => $file) {
        $this->prepareTemplate($file);
      }
      $cssFiles = $this->getFiles('../'.THEME_DIR, 'css');
      foreach ($cssFiles as $key => $file) {
        $this->prepareCss($file);
      }
      
      rename('../'.THEME_DIR.'default_content.css', '../'.THEME_DIR.'ip_content.css');
              
      //header("location: ".$navigation->generateLink($navigation->curStep(), $navigation->curScript(), $navigation->curAction() + 1));
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

  public function prepareTemplate ($file) {
    $template = file_get_contents($file);
    
    $template = str_replace('.contentModTextPhotoImage', '.ipWidgetTextPhotoImageLeft', $template);
    $template = str_replace('contentModSeparator', 'ipWidgetSeparatorLine', $template);
    $template = str_replace('contentMod', 'ipWidget', $template);
    $template = str_replace('standard/content_management/modules/', 'standard/content_management/widgets/', $template);
    $template = str_replace('\\Modules\\standard\\content_management\\Modules\\', '\\Modules\\standard\\content_management\\Widgets\\', $template);
    $template = str_replace('default_content.css', 'ip_content.css', $template);
    
    file_put_contents($file, $template);    
  }
    
  public function prepareCss ($file) {
    $css = file_get_contents($file);
    
    $css = str_replace('.contentModTextPhotoImage', '.ipWidgetTextPhotoImageLeft', $css);
    $css = str_replace('contentModSeparator', 'ipWidgetSeparatorLine', $css);
    $css = str_replace('contentMod', 'ipWidget', $css);

    if(substr($file, -19) == 'default_content.css') {
      $css .= '
/*ip 1.0.3 beta update*/
    
.ipContent .ipWidgetSeparatorSpace {
  height: 10px;
}

.ipContent .ipWidgetTextPhotoImageRight {
  float: right;
  margin: 0 0 10px 10px;
  padding: 3px;
  border: 1px solid #cccccc;
}

.ipContent .ipWidgetTextPhotoImageRight:hover {
  border: 1px solid #999999;
}

.ipContent .ipWidgetTextPhotoImageRight img {
  float: right;
  border: none;
}

.ipContent .ipWidgetTextPhotoImageSmallLeft{
  float: left;
  margin: 0 10px 10px 0;
  width: 100px;
  border: 1px solid #cccccc;
}

.ipContent .ipWidgetTextPhotoImageSmallRight{
  float: right;
  margin: 0 0 10px 10px;
  width: 100px;
  border: 1px solid #cccccc;
}    
';    
      
    }
    
    file_put_contents($file, $css);    
  }
      
  
  public function cancelWriteThemes () {
    global $navigation;
    global $htmlOutput;
    
    $answer = '';
      
    if (is_writeable ('../'.THEME_DIR)) {
      $answer .= CANCEL_TEMPLATE_WRITEABLE;
      $answer .= '<br/>';
      $answer .= '<input onClick="skipStepCheck(this)" class="chbx" type="checkbox" name="later" />'.LATER.'';
      $answer .= '<br/>';
      $answer .= '<br/>';
      $answer .= '<br/>';
      $answer .= '<br/>';
      $answer .= '<div id="self">'.$htmlOutput->button(IP_NEXT, $navigation->generateLink($navigation->curStep(), $navigation->curScript(), $navigation->curAction())).'</div>';
      $answer .= '<div id="next">'.$htmlOutput->button(IP_NEXT, $navigation->generateLink($navigation->curStep(), $navigation->curScript(), $navigation->curAction()+1)).'</div>';
      $answer .= '
      <script type="text/javascript">
        function skipStepCheck(checkbox){
          if(checkbox && checkbox.checked){
            document.getElementById(\'self\').style.display = \'none\';
            document.getElementById(\'next\').style.display = \'\';
          }else {
            document.getElementById(\'self\').style.display = \'\';
            document.getElementById(\'next\').style.display = \'none\';
          }
        } 
        skipStepCheck();
      </script>
      ';
      
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
    if (\Db_100::getSystemVariable('version') != '1.0.3 Beta') {
      require_once('parameters_refractor.php');
      $parametersRefractor= new ParametersRefractor();
      $parametersRefractor->execute();
      
      if ($this->curStep == $this->stepCount){
        \Db_100::setSystemVariable('version','1.0.3 Beta');
      }      
    }
    
    if ($this->curStep == $this->stepCount) {
      header("location: ".$navigation->generateLink($navigation->curStep() + 1));
    } else {
      header("location: ".$navigation->generateLink($navigation->curStep(), $navigation->curScript() + 1));
    }
      
    return $answer;
  }
  

  function getFiles($dir, $extension) {
    $answer = array();
    if ($handle = opendir($dir)) { 
      while (false !== ($file = readdir($handle))) {
        if(is_dir($dir.$file) && $file != ".." && $file != ".") {
          $answer = array_merge($answer, $this->getFiles($dir.$file, $extension));
        } else {
          if($file != ".." && $file != "."){
            if (strtolower(substr($file, -(strlen($extension)+1))) == '.'.$extension){
              $answer[] = $dir.'/'.$file;
            }
          }
        }
      }
      closedir($handle);
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
  
    
  

}

