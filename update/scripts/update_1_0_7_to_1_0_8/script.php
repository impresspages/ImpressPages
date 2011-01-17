<?php
/**
 * @package   ImpressPages
 * @copyright Copyright (C) 2011 ImpressPages LTD.
 * @license   GNU/GPL, see ip_license.html
 */
namespace update_1_0_7_to_1_0_8;

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
    $this->deleteFolders[] = 'nbproject';


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
    return 4;
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
        $answer .= $this->updateRobots();
      break;
      case 4:
        $answer .= $this->updateDatabase();
      break;
    }


    return $answer;
  }


  public function updateRobots() {
    global $navigation;
    global $htmlOutput;

    $answer = '';

    $robotsFile = '../robots.txt';
    if (is_writable($robotsFile)) {

      $data = file($robotsFile, FILE_IGNORE_NEW_LINES);
      $newData = '';
      foreach($data as $dataKey => $dataVal) {
        $tmpVal = $dataVal;
        $tmpVal = trim($tmpVal);
        $tmpVal = str_replace('User-Agent:', 'User-agent:', $tmpVal);

        $tmpVal =  preg_replace('/^User-Agent:(.*)/', 'User-agent:${0}', $tmpVal);
        $tmpVal =  preg_replace('/^Disallow: \/ip_cms$/', 'Disallow: /ip_cms/', $tmpVal);
        $tmpVal =  preg_replace('/^Disallow: \/ip_configs$/', 'Disallow: /ip_configs/', $tmpVal);
        $tmpVal =  preg_replace('/^Disallow: \/update$/', 'Disallow: /update/', $tmpVal);
        $tmpVal =  preg_replace('/^Disallow: \/install$/', 'Disallow: /install/', $tmpVal);
        $tmpVal =  preg_replace('/^Sitemap:(.*)/', 'Sitemap: '.BASE_URL.'sitemap.php', $tmpVal);
        $newData .= $tmpVal."\n";
      }

      file_put_contents($robotsFile, $newData);

      header("location: ".$navigation->generateLink($navigation->curStep(), $navigation->curScript(), $navigation->curAction() + 1));
    } else {
      $answer .= MAKE_ROBOTS_WRITEABLE;
      $answer .= "<br/>";
      $answer .= "<br/>";
      $answer .= "<br/>";
      $answer .= "<br/>";
      $answer .= "<br/>";
      $answer .= $htmlOutput->button(IP_NEXT, $navigation->generateLink($navigation->curStep(), $navigation->curScript(), $navigation->curAction()));
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
    require_once(__DIR__.'/db.php');
    require_once(__DIR__.'/../update_1_0_2_beta_to_1_0_3_beta/parameters_refractor.php');

    $answer = '';
    if (\Db_100::getSystemVariable('version') != '1.0.8') {


      $parametersRefractor = new \update_1_0_2_beta_to_1_0_3_beta\ParametersRefractor();


      //add table widget
      $widgetGroup = Db::getWidgetGroup('text_photos');
      $newWidget = array(
          'name' => 'table',
          'group_id' => $widgetGroup['id'],
          'dynamic' => 0,
          'translation' => 'Table',
          'version' => '1.00',
          'row_number' => Db::getMaxWidgetGroupRow($widgetGroup['id']) + 1
      );

      Db::addWidget($widgetGroup['id'], $newWidget);

      //add parameters group
      $module = \Db_100::getModule(null, 'standard', 'content_management');
      $parameterGroup = $parametersRefractor->getParametersGroup($module['id'], 'widget_table');
      if ($parameterGroup) {
        $parameterGroupId = $parameterGroup['id'];
      } else {
        $parameterGroupId = $parametersRefractor->addParameterGroup($module['id'], 'widget_table', 'Widget table', 1);
      }
      if(!\Db_100::getParameter('standard', 'content_management', 'widget_table', 'widget_title')) {
        \Db_100::addStringParameter($parameterGroupId, 'Widget title', 'widget_title', 'Table', 1);
      }





      Db::createTableWidgetTables();

      //upate contact form widget
      Db::updateContactFormWidget();


      //update contact form widget parameters
      $parametersRefractor->deleteParameter('standard', 'content_management', 'widget_contact_form', 'text_field');
      $parametersRefractor->deleteParameter('standard', 'content_management', 'widget_contact_form', 'text_row');
      $module = \Db_100::getModule(null, 'standard', 'content_management');
      $group = $parametersRefractor->getParametersGroup($module['id'], 'widget_contact_form');
      if($group) {
        if(!\Db_100::getParameter('standard', 'content_management', 'widget_contact_form', 'text')) {
          \Db_100::addStringParameter($group['id'], 'Text', 'text', 'Text', 1);
        }
        if(!\Db_100::getParameter('standard', 'content_management', 'widget_contact_form', 'text_multiline')) {
          \Db_100::addStringParameter($group['id'], 'Text (multiline)', 'text_multiline', 'Text (multiline)', 1);
        }
        if(!\Db_100::getParameter('standard', 'content_management', 'widget_contact_form', 'select')) {
          \Db_100::addStringParameter($group['id'], 'Select', 'select', 'Select', 1);
        }
        if(!\Db_100::getParameter('standard', 'content_management', 'widget_contact_form', 'checkbox')) {
          \Db_100::addStringParameter($group['id'], 'Checkbox', 'checkbox', 'Checbox', 1);
        }
        if(!\Db_100::getParameter('standard', 'content_management', 'widget_contact_form', 'radio')) {
          \Db_100::addStringParameter($group['id'], 'Radio', 'radio', 'Radio', 1);
        }
        if(!\Db_100::getParameter('standard', 'content_management', 'widget_contact_form', 'values_popup_title')) {
          \Db_100::addStringParameter($group['id'], 'Values popup title', 'values_popup_title', 'Values', 1);
        }
        if(!\Db_100::getParameter('standard', 'content_management', 'widget_contact_form', 'values_field_title')) {
          \Db_100::addStringParameter($group['id'], 'Values field title', 'values_field_title', 'Enter available values. Each value on a new line.', 1);
        }
      }

      $group = $parametersRefractor->getParametersGroup($module['id'], 'admin_translations');
      if ($group) {
        if(!\Db_100::getParameter('standard', 'content_management', 'admin_translations', 'warning_not_saved')) {
          \Db_100::addStringParameter($group['id'], 'Warning not saved', 'warning_not_saved', 'Your changes are not saved', 1);
        }
        if(!\Db_100::getParameter('standard', 'content_management', 'admin_translations', 'saved')) {
          \Db_100::addStringParameter($group['id'], 'Saved', 'saved', 'Saved', 1);
        }
        if(!\Db_100::getParameter('standard', 'content_management', 'admin_translations', 'save_now')) {
          \Db_100::addStringParameter($group['id'], 'Save now', 'save_now', 'Save now', 1);
        }
      }


      $module = \Db_100::getModule(null, 'standard', 'configuration');
      $group = $parametersRefractor->getParametersGroup($module['id'], 'system_translations');
      if($group) {
        if(!\Db_100::getParameter('standard', 'configuration', 'system_translations', 'system_message')) {
          \Db_100::addStringParameter($group['id'], 'System message', 'system_message', 'System message', 1);
        }
        if(!\Db_100::getParameter('standard', 'configuration', 'system_translations', 'help')) {
          \Db_100::addStringParameter($group['id'], 'Help', 'help', 'Help', 1);
        }
      }

      if ($this->curStep == $this->stepCount){
        \Db_100::setSystemVariable('version','1.0.8');
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

