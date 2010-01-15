<?php

/**
 * @package   ImpressPages
 * @copyright Copyright (C) 2009 JSC Apro media.
 * @license   GNU/GPL, see ip_license.html
 */
namespace update_1_0_2_beta_to_1_0_3_beta;



if (!defined('CMS')) exit;

class ParametersRefractor {
   
  
  public function __construct() {
  }
  
  public function execute(){
    //refract content module parameters to standard
    $parameterGroups = $this->getContentModuleGroups();
    $usedModules = array();
    $contentModuleId = $this->getModuleId('standard', 'content_management');
    if($parameterGroups){
      foreach($parameterGroups as $key => $group){
        if (isset($usedModules[$group['content_module_id']])) {
          $this->changeParametersGroup($group['id'], $usedModules[$group['content_module_id']]);
          $this->deleteParametersGroup($group['id']);
        } else {
          $widget = $this->getWidget($group['content_module_id']);
          $widgetGroup = $this->getWidgetGroup($widget['group_id']);
          $this->changeGroupModuleId($group['id'], $contentModuleId);
          $this->changeGroupTranslation($group['id'], 'Widget: '.$widget['translation']);
          $this->changeGroupName($group['id'], 'widget_'.$widget['name']);
          
          $usedModules[$group['content_module_id']] = $group['id'];
        }
      }
    }
    $this->removeContentModuleColumn();
    
    //adnd empty parameters translations
    $this->addEmptyTranslations('quality', 'Photo quality');
    $this->addEmptyTranslations('big_height', 'Big photo height');
    $this->addEmptyTranslations('big_width', 'Big photo width');
    $this->addEmptyTranslations('big_quality', 'Big photo quality');
    $this->addEmptyTranslations('width', 'Photo width');
    $this->addEmptyTranslations('height', 'Photo Height');
    
    //delete modules
    $this->destroyModule('developer', 'content_modules_configuration');
    $this->destroyModule('developer', 'content_mod_config_exp_imp');
    
    //rename module
    $widgetsModule = $this->getModuleId('developer', 'content_modules');
    $this->renameModule($widgetsModule['id'], 'widgets', 'Widgets');
    
    //add backend language parameter
    $configurationModuleId = $this->getModuleId('standard', 'configuration');
    $groups = $this->getModuleGroups($configurationModuleId);
    foreach($groups as $group){
      if($group['name'] == 'advanced_options'){
        if(!$this->getParameter($group['id'], 'administrator_interface_language'))
          $this->addStringParameter($group['id'], 'Backend language', 'administrator_interface_language', 'en', 1);
      }
    }
    
    //rename content modules to widgets
    /*$this->renameTable("cm_misc_contact_form", "w_form");    
    $this->renameTable("cm_misc_contact_form_field", "w_form_field");    
    $this->renameTable("cm_misc_file", "w_file");    
    $this->renameTable("cm_misc_html_code", "w_html_code");    
    $this->renameTable("cm_misc_rich_text", "w_rich_text");    
    $this->renameTable("cm_misc_video", "w_video");    
    
    $this->renameTable("cm_text_photos_faq", "w_faq");    
    $this->renameTable("cm_text_photos_logo_gallery", "w_logo_gallery");    
    $this->renameTable("cm_text_photos_logo_gallery_logo", "w_logo_gallery_logo");    
    $this->renameTable("cm_text_photos_photo", "w_photo");    
    $this->renameTable("cm_text_photos_photo_gallery", "w_photo_gallery");    
    $this->renameTable("cm_text_photos_photo_gallery_photo", "w_photo_gallery_photo");    
    $this->renameTable("cm_text_photos_text", "w_text");    
    $this->renameTable("cm_text_photos_text_photo", "w_text_photo");    
    $this->renameTable("cm_text_photos_text_title", "w_text_title");    
    $this->renameTable("cm_text_photos_title", "w_title");    */
    
    //new std_mod parameter
    $moduleId = $this->getModuleId('developer', 'std_mod');
    $group = $this->getParametersGroup($moduleId, 'admin_translations'); 
    $this->addStringParameter($group['id'], 'Passwords do not match', 'passwords_do_not_match', 'Passwords do not match', 1);    

    
    //new modules install parameters
    $moduleId = $this->getModuleId('developer', 'modules');
    $group = $this->getParametersGroup($moduleId, 'admin_translations_install'); 
    $this->addStringParameter($group['id'], 'Error: incorrect ini file ', 'error_incorrect_ini_file', 'Incorrect plugin.ini file: ', 1);    
    $this->addStringParameter($group['id'], 'Error: update required', 'error_update_required', 'This module requires another module to be updated: ', 1);    
    $this->addStringParameter($group['id'], 'Error: ini file does not exist', 'error_ini_file_doesnt_exist', 'Ini file does not exist: ', 1);    
        
    if(!$this->getParametersGroup($moduleId, 'admin_translations')){
      $groupId = $this->addParameterGroup($moduleId, 'admin_translations', 'Admin translations', 1);
      $this->addStringParameter($groupId, 'Error: can\'t delete core module', 'error_cant_delete_core_module', 'Can\'t delete core module: ', 1);
      $this->addStringParameter($groupId, 'Error: delete required module', 'error_delete_required_module', 'This module is used by another module: ', 1);
    }
    
    
    //add widgets titles
    $configurationModuleId = $this->getModuleId('standard', 'content_management');
    
    if(!$this->getParametersGroup($configurationModuleId, 'widget_separator')){
      $groupId = $this->addParameterGroup($configurationModuleId, 'widget_separator', 'Widget: Separator', 1);
      $this->addStringParameter($groupId, 'Widget title', 'widget_title', 'Separator', 1);
    }
    
    if(!$this->getParametersGroup($configurationModuleId, 'widget_rich_text')){
      $groupId = $this->addParameterGroup($configurationModuleId, 'widget_rich_text', 'Widget: Rich text', 1);
      $this->addStringParameter($groupId, 'Widget title', 'widget_title', 'Rich text', 1);
    }
    
    $groups = $this->getModuleGroups($configurationModuleId);
    foreach($groups as $group){
      switch($group['name']){
        case 'widget_contact_form':
          if(!$this->getParameter($group['id'], 'widget_title'))
            $this->addStringParameter($group['id'], 'Widget title', 'widget_title', 'Contact form', 1);
        break;
        case 'widget_faq':
          if(!$this->getParameter($group['id'], 'widget_title'))
            $this->addStringParameter($group['id'], 'Widget title', 'widget_title', 'FAQ', 1);
        break;
        case 'widget_file':
          if(!$this->getParameter($group['id'], 'widget_title'))
            $this->addStringParameter($group['id'], 'Widget title', 'widget_title', 'File', 1);
        break;
        case 'widget_html_code':
          if(!$this->getParameter($group['id'], 'widget_title'))
            $this->addStringParameter($group['id'], 'Widget title', 'widget_title', 'HTML code', 1);
        break;
        case 'widget_logo_gallery':
          if(!$this->getParameter($group['id'], 'widget_title'))
            $this->addStringParameter($group['id'], 'Widget title', 'widget_title', 'Logo gallery', 1);
        break;
        case 'widget_photo_gallery':
          if(!$this->getParameter($group['id'], 'widget_title'))
            $this->addStringParameter($group['id'], 'Widget title', 'widget_title', 'Photo gallery', 1);
        break;
        case 'widget_photo':
          if(!$this->getParameter($group['id'], 'widget_title'))
            $this->addStringParameter($group['id'], 'Widget title', 'widget_title', 'Photo', 1);
        break;
        case 'widget_text':
          if(!$this->getParameter($group['id'], 'widget_title'))
            $this->addStringParameter($group['id'], 'Widget title', 'widget_title', 'Text', 1);
        break;
        case 'widget_text_photo':
          if(!$this->getParameter($group['id'], 'widget_title'))
            $this->addStringParameter($group['id'], 'Widget title', 'widget_title', 'Text/photo', 1);
        break;
        case 'widget_title':
          if(!$this->getParameter($group['id'], 'widget_title'))
            $this->addStringParameter($group['id'], 'Widget title', 'widget_title', 'Title', 1);
        break;
        case 'widget_text_title':
          if(!$this->getParameter($group['id'], 'widget_title')) {
            $this->changeGroupAdmin($group['id'], 1);
            $this->addStringParameter($group['id'], 'Widget title', 'widget_title', 'Text/title', 1);
          }
        break;
        case 'widget_video':
          if(!$this->getParameter($group['id'], 'widget_title'))
            $this->addStringParameter($group['id'], 'Widget title', 'widget_title', 'Video', 1);
        break;
      }
    }
    
    //localization module    
    $developerGroup = $this->getModuleGroup('developer');
    if($this->getModuleId('developer', 'localization') == false) {
      //get new row_number
      $currentModules = $this->getModules($developerGroup['id']);
      $rowNumber = -9223372036854775808; //minimal value
      foreach($currentModules as $module){
        if($module['row_number'] >= $rowNumber)
          $rowNumber = 1 + $module['row_number'];
      }
      //get new row_number
      $moduleId = $this->addModule($developerGroup['id'], 'Localization', 'localization', 1, 1, 1, '1.0.0', $rowNumber);
      $users = $this->getUsers();
      foreach($users as $user){
        $this->addPermissions($moduleId, $user['id']);
      }
      $groupId = $this->addParameterGroup($moduleId, 'admin_translations', 'Admin translations', 1);
      $this->addStringParameter($groupId, 'Administrator interface', 'administrator_interface', 'Administrator interface', 1);
      $this->addStringParameter($groupId, 'Export language file', 'export_language_file', 'Export language file', 1);
      $this->addStringParameter($groupId, 'Import language file', 'import_language_file', 'Import language file', 1);
      $this->addStringParameter($groupId, 'Preview', 'preview', 'Preview', 1);
      $this->addStringParameter($groupId, 'Public interface', 'public_interface', 'Public interface', 1);
    }
    
    
    $contentModuleId = $this->getModuleId('developer', 'content_modules');
    $this->updateModule($contentModuleId, 'Widgets', 'widgets');
    
    
    
    
    //delete parameters
    $this->deleteParameter("developer", "zones", "admin_translations", "default");
    $this->deleteParameter("developer", "zones", "admin_translations", "depth");
    $this->deleteParameter("developer", "zones", "admin_translations", "inactive_depth");
    $this->deleteParameter("developer", "zones", "admin_translations", "inactive_if_parent");
    $this->deleteParameter("developer", "zones", "admin_translations", "keywords");
    $this->deleteParameter("developer", "zones", "admin_translations", "managed");
    $this->deleteParameter("developer", "zones", "admin_translations", "sitemap");
    $this->deleteParameter("developer", "zones", "admin_translations", "title");
    $this->deleteParameter("standard", "content_management", "admin_translations", "man_paragraph_new");
    $this->deleteParameter("standard", "menu_management", "admin_translations", "rename");
    $this->deleteParameter("standard", "menu_management", "admin_translations", "show");
    $this->deleteParameter("standard", "menu_management", "admin_translations", "hide");
    
    
    
    
    //widget layouts
    
    $this->addLayoutField('mc_misc_contact_form', 'default');
    $this->addLayoutField('mc_misc_file', 'default');
    $this->addLayoutField('mc_misc_html_code', 'default');
    $this->addLayoutField('mc_misc_rich_text', 'default');
    $this->addLayoutField('mc_misc_video', 'default');
    
    $this->addLayoutField('mc_text_photos_faq', 'default');
    $this->addLayoutField('mc_text_photos_logo_gallery', 'default');
    $this->addLayoutField('mc_text_photos_photo', 'default');
    $this->addLayoutField('mc_text_photos_photo_gallery', 'default');
    $this->addLayoutField('mc_text_photos_text', 'default');
    $this->addLayoutField('mc_text_photos_text_photo', 'left');
    $this->addLayoutField('mc_text_photos_text_title', 'default');
    $this->addLayoutField('mc_text_photos_title', 'default');
    
    
    if($this->addSeparatorTable()){
      $this->fillSeparatorTable();
    }


    $moduleId = $this->getModuleId('standard', 'content_management');
    $group = $this->getParametersGroup($moduleId, 'widget_contact_form'); 
    $this->addStringParameter($group['id'], 'Layout default', 'layout_default', 'Default', 1);
    
    $group = $this->getParametersGroup($moduleId, 'widget_faq'); 
    $this->addStringParameter($group['id'], 'Layout default', 'layout_default', 'Default', 1);
        
    $group = $this->getParametersGroup($moduleId, 'widget_file'); 
    $this->addStringParameter($group['id'], 'Layout default', 'layout_default', 'Default', 1);
    
    $group = $this->getParametersGroup($moduleId, 'widget_html_code'); 
    $this->addStringParameter($group['id'], 'Layout default', 'layout_default', 'Default', 1);
    
    $group = $this->getParametersGroup($moduleId, 'widget_logo_gallery'); 
    $this->addStringParameter($group['id'], 'Layout default', 'layout_default', 'Default', 1);
    
    $group = $this->getParametersGroup($moduleId, 'widget_photo'); 
    $this->addStringParameter($group['id'], 'Layout default', 'layout_default', 'Default', 1);
    
    $group = $this->getParametersGroup($moduleId, 'widget_photo_gallery'); 
    $this->addStringParameter($group['id'], 'Layout default', 'layout_default', 'Default', 1);
    
    $group = $this->getParametersGroup($moduleId, 'widget_rich_text'); 
    $this->addStringParameter($group['id'], 'Layout default', 'layout_default', 'Default', 1);
    
    $group = $this->getParametersGroup($moduleId, 'widget_separator'); 
    $this->addStringParameter($group['id'], 'Layout line', 'layout_line', 'Line', 1);
    $this->addStringParameter($group['id'], 'Layout space', 'layout_space', 'Space', 1);
    
    $group = $this->getParametersGroup($moduleId, 'widget_text'); 
    $this->addStringParameter($group['id'], 'Layout default', 'layout_default', 'Default', 1);
    
    $group = $this->getParametersGroup($moduleId, 'widget_text_photo'); 
    $this->addStringParameter($group['id'], 'Layout left', 'layout_left', 'Left', 1);
    $this->addStringParameter($group['id'], 'Layout right', 'layout_right', 'Right', 1);
    $this->addStringParameter($group['id'], 'Layout small left', 'layout_small_left', 'Small left', 1);
    $this->addStringParameter($group['id'], 'Layout small right', 'layout_small_right', 'Small right', 1);
    
    $group = $this->getParametersGroup($moduleId, 'widget_title'); 
    $this->addStringParameter($group['id'], 'Layout default', 'layout_default', 'Default', 1);
    
    $group = $this->getParametersGroup($moduleId, 'widget_text_title'); 
    $this->addStringParameter($group['id'], 'Layout default', 'layout_default', 'Default', 1);
    
    $group = $this->getParametersGroup($moduleId, 'widget_video'); 
    $this->addStringParameter($group['id'], 'Layout default', 'layout_default', 'Default', 1);
    
    
    
    //titles in seo module where zone title is empty
    $this->addTitles();
   
  }
  
  private function addTitles(){
    $sql = "update `".DB_PREF."zone_parameter`
    set `title` = CONCAT( UPPER( SUBSTRING( `url`, 1, 1 ) ) , SUBSTRING( `url`, 2 ) ) where `title` = ''";
    $rs = mysql_query($sql);
    if(!$rs){
      trigger_error($sql." ".mysql_error());
    }
    
  }
  
  private function fillSeparatorTable(){
    $separatorInstances = $this->getSeparatorInstances();
    foreach($separatorInstances as $instance){
      $sql = "insert into ".DB_PREF."mc_text_photos_separator set layout='line'";
      $rs = mysql_query($sql);
      if(!$rs) 
        trigger_error($sql." ".mysql_error());
      
      $sql = "update ".DB_PREF."content_element_to_modules set module_id = '".mysql_insert_id()."' where id = ".(int)$instance['id']."";
      $rs = mysql_query($sql);
      if(!$rs) 
        trigger_error($sql." ".mysql_error());
    }
  }
  
  private function addSeparatorTable(){
    $sql = "

CREATE TABLE `".DB_PREF."mc_text_photos_separator` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `layout` varchar(255) NOT NULL DEFAULT 'line',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=20 ;    
    ";
    $rs = mysql_query($sql);
    if(!$rs){ 
      //trigger_error($sql." ".mysql_error()); //trows unnecesary errors if executed twice.
      return false;
    } else {
      return true;
    }
  }
  
  private function getSeparatorInstances(){
    $answer = array();
    $sql = "select * from ".DB_PREF."content_element_to_modules where group_key = 'text_photos' and module_key = 'separator' ";
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
  
  private function addLayoutField($table, $default){
    $sql = "alter table  `".DB_PREF.$table."` ADD  `layout` VARCHAR( 255 ) NOT NULL DEFAULT  '".mysql_real_escape_string($default)."'";
    $rs = mysql_query($sql);
    /*if(!$rs) //trows unnecesary errors if executed twice.
      trigger_error($sql." ".mysql_error());*/
  }
  
  private function deleteParameter($moduleGroup, $module, $parameterGroup, $parameterName){
    $moduleId = $this->getModuleId($moduleGroup, $module);
    $parameterGroup = $this->getParametersGroup($moduleId, $parameterGroup);
    $parameter = $this->getParameter($parameterGroup['id'], $parameterName);
    
    if($parameter){
      $sql = false;
      switch($parameter['type']){
        case 'string_wysiwyg':
        case 'string':
        case 'textarea':
          $sql = "delete from ".DB_PREF."par_string where parameter_id = ".(int)$parameter['id']."";
        break;
        case 'integer':
          $sql = "delete from ".DB_PREF."par_integer where parameter_id = ".(int)$parameter['id']."";
        break;
        case 'bool':
          $sql = "delete from ".DB_PREF."par_bool where parameter_id = ".(int)$parameter['id']."";
        break;
        case 'lang':
        case 'lang_textarea':
        case 'lang_wysiwyg':
          $sql = "delete from ".DB_PREF."par_lang where parameter_id = ".(int)$parameter['id']."";
        break;
      }
      
      if($sql){
        $rs = mysql_query($sql);
        if(!$rs)
          trigger_error($sql.' '.mysql_error());
        $sql = "delete from ".DB_PREF."parameter where id = ".(int)$parameter['id']."";
        $rs = mysql_query($sql);
        if(!$rs)
          trigger_error($sql.' '.mysql_error());

      }
    }
  }
  
  private function updateModule($moduleId, $newTranslation, $newName){
    $sql =  "update ".DB_PREF."module set translation = '".mysql_real_escape_string($newTranslation)."', name = '".mysql_real_escape_string($newName)."' where id = ".(int)$moduleId." ";
    $rs = mysql_query($sql);
    if($rs){
      return true;
    } else {
      trigger_error($sql." ".mysql_error());  
      return false;    
    } 
  }
  
  private function addPermissions($moduleId, $userId){
    $sql = "insert into ".DB_PREF."user_to_mod
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
  
  private function getUsers(){
    $answer = array();
    $sql = "select * from ".DB_PREF."user where 1";
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
  
  private function addModule($groupId, $moduleTranslation, $moduleName, $admin, $managed, $core, $version, $rowNumber = 0){
    $sql = "insert into ".DB_PREF."module
    set
    group_id = '".(int)$groupId."',
    name = '".mysql_real_escape_string($moduleName)."',
    translation = '".mysql_real_escape_string($moduleTranslation)."',
    admin = '".(int)$admin."',
    managed = '".(int)$managed."',
    core = '".(int)$core."',
    row_number = '".(int)$rowNumber."',
    version = '".mysql_real_escape_string($version)."'
    
    ";
    $rs = mysql_query($sql);
    if($rs){
      return mysql_insert_id();
    } else {
      trigger_error($sql." ".mysql_error());  
      return false;    
    }
  }
 
  
  private function getParameter($groupId, $name){
    $answer = array();
    $sql = "select * from ".DB_PREF."parameter where `group_id` = ".(int)$groupId." and `name` = '".mysql_real_escape_string($name)."'";
    $rs = mysql_query($sql);
    if($rs){
      if($lock = mysql_fetch_assoc($rs)){
        return $lock;
      } else
        return false;
    } else {
      trigger_error($sql." ".mysql_error());  
      return false;    
    }
  }  
  
  private function renameTable($oldName, $newName){
    $sql = "RENAME TABLE `".DB_PREF.$oldName."`  TO `".DB_PREF.$newName."`" ;
    $rs = mysql_query($sql);
    if($rs){
      return true;
    } else {
      //trigger_error($sql." ".mysql_error());  
      return false;    
    }
  }
  
  private function addStringParameter($groupId, $translation, $name, $value, $admin){
    $sql = "INSERT INTO `".DB_PREF."parameter` (`name`, `admin`, `regexpression`, `group_id`, `translation`, `comment`, `type`)
    VALUES ('".mysql_real_escape_string($name)."', ".(int)$admin.", '', ".(int)$groupId.", '".mysql_real_escape_string($translation)."', NULL, 'string')";
    $rs = mysql_query($sql);
    if($rs){
      $sql2 = "INSERT INTO `".DB_PREF."par_string` (`value`, `parameter_id`)
      VALUES ('".mysql_real_escape_string($value)."', ".mysql_insert_id().");";
      $rs2 = mysql_query($sql2);
      if($rs2) {
          return true;
      } else {
        trigger_error($sql2." ".mysql_error());  
        return false;    
      }
    } else {
      trigger_error($sql." ".mysql_error());  
      return false;    
    }
    
  }
  
  private function destroyModule($groupName, $moduleName){
    $id = $this->getModuleId($groupName, $moduleName);
/*    $groups = $this->getModuleGroups($id);
    foreach ($groups as $group) {
      $this->deleteParameters($group['id']);
      $this->deleteParametersGroup($group['id']);
    }*/
    $this->deleteModule($id);
    $this->deletePermissions($id);
  }
  
  
  private function deletePermissions($moduleId){
    $sql = "delete from ".DB_PREF."user_to_mod where `module_id` = ".(int)$moduleId." ";
    $rs = mysql_query($sql);
    if($rs){
      return true;
    } else {
      trigger_error($sql." ".mysql_error());  
      return false;    
    }
  }
    
  
  private function getModuleGroup($name){
    $sql = "select * from ".DB_PREF."module_group where name = '".mysql_real_escape_string($name)."' ";
    $rs = mysql_query($sql);
    if($rs){
      if($lock = mysql_fetch_assoc($rs)){
        return $lock;
      } else {
        return false;
      }
    } else {
      trigger_error($sql." ".mysql_error());  
      return false;    
    }  
  }
  
/*  private function moveModuleTranslationsToParameters(){
    $modulesModule = $this->getModuleId('developer', 'modules');
    $moduleNamesId = $this->newGroup('module_names', 'Module names', $modulesModule['id'], 1);
    $moduleGroupsId = $this->newGroup('group_names', 'Group names', $modulesModule['id'], 1);
    
    
    $moduleGroups = $this->getModuleGroups();
    
    foreach($moduleGroups as $moduleGroup) {
      $modules = $this->getModules($moduleGroup['id']);
      $this->newStringParameter($moduleGroup['name'], $moduleGroup['translation'], $moduleGroupsId);
      foreach ($modules as $module){
        $this->newStringParameter($moduleGroup['name'].'_'.$module['name'], $moduleGroup['translation'].' -> '.$module['translation'], $moduleNamesId);
      }
    }
    
  }
  
  private function newStringParameter($name, $translation, $group_id, $admin){
    $sql = "insert into ".DB_PREF."parameter_group
    set `name`= '".mysql_real_escape_string()."',
    `translation`= '".mysql_real_escape_string()."',
    `module_id`= ".(int)$module_id.",
    `admin`= ".(int)$admin."";
    $rs = mysql_query($sql);
    if($rs){
      return mysql_insert_id();
    } else {
      trigger_error($sql." ".mysql_error());  
      return false;    
    }
  }  
  private function getModuleGroups(){
    $answer = array();
    $sql = "select * from ".DB_PREF."module_group  where 1";
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
*/  
  private function getModules($groupId){
    $answer = array();
    $sql = "select * from ".DB_PREF."module where `group_id` = ".(int)$groupId." ";
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
  
  
  private function dropTable($name) {
    $sql = "DROP TABLE `".DB_PREF.$name."`";
    $rs = mysql_query($sql);
    if($rs){
      return true;
    } else {
      //trigger_error($sql." ".mysql_error());  
      return false;    
    }
  }
  
  private function getContentModuleGroups(){
    $answer = array();
    $sql = "select * from ".DB_PREF."parameter_group where `content_module_id` is not null";
    $rs = mysql_query($sql);
    if($rs){
      while($lock = mysql_fetch_assoc($rs)){
        $answer[] = $lock;
      }
      return $answer;
    } else {
      //trigger_error($sql." ".mysql_error());  
      return false;    
    }
  }
  
  private function getModuleGroups($moduleId){
    $answer = array();
    $sql = "select * from ".DB_PREF."parameter_group where `module_id` = ".(int)$moduleId."";
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
  
  private function deleteParametersGroup($id){
    $sql = "delete from ".DB_PREF."parameter_group where `id` = ".(int)$id." ";
    $rs = mysql_query($sql);
    if($rs){
      return true;
    } else {
      trigger_error($sql." ".mysql_error());  
      return false;    
    }
  }
  
  private function newGroup($name, $translation, $module_id, $admin){
    $sql = "insert into ".DB_PREF."parameter_group
    set `name`= '".mysql_real_escape_string()."',
    `translation`= '".mysql_real_escape_string()."',
    `module_id`= ".(int)$module_id.",
    `admin`= ".(int)$admin."";
    $rs = mysql_query($sql);
    if($rs){
      return mysql_insert_id();
    } else {
      trigger_error($sql." ".mysql_error());  
      return false;    
    }
  }
  
  private function changeGroupModuleId($groupId, $newModuleId){
    $sql = "update ".DB_PREF."parameter_group set `module_id` = ".(int)$newModuleId." where `id` = ".(int)$groupId." ";
    $rs = mysql_query($sql);
    if($rs){
      return true;
    } else {
      trigger_error($sql." ".mysql_error());  
      return false;    
    }
  }
  
  private function changeGroupTranslation($groupId, $translation){
    $sql = "update ".DB_PREF."parameter_group set `translation` = '".mysql_real_escape_string($translation)."' where `id` = ".(int)$groupId." ";
    $rs = mysql_query($sql);
    if($rs){
      return true;
    } else {
      trigger_error($sql." ".mysql_error());  
      return false;    
    }
  }

  private function changeGroupAdmin($groupId, $admin){
    $sql = "update ".DB_PREF."parameter_group set `admin` = '".(int)$admin."' where `id` = ".(int)$groupId." ";
    $rs = mysql_query($sql);
    if($rs){
      return true;
    } else {
      trigger_error($sql." ".mysql_error());  
      return false;    
    }
  }  

  private function addParameterGroup($moduleId, $name, $translation, $admin){
    $sql = "insert into ".DB_PREF."parameter_group 
    set 
    `name` = '".mysql_real_escape_string($name)."',
    `translation` = '".mysql_real_escape_string($translation)."',
    `module_id` = '".(int)$moduleId."',
    `admin` = '".(int)$admin."'
    ";
    $rs = mysql_query($sql);
    if($rs){
      return mysql_insert_id();
    } else {
      trigger_error($sql." ".mysql_error());  
      return false;    
    }
  }
  
  
  private function changeGroupName($groupId, $name){
    $sql = "update ".DB_PREF."parameter_group set `name` = '".mysql_real_escape_string($name)."' where `id` = ".(int)$groupId." ";
    $rs = mysql_query($sql);
    if($rs){
      return true;
    } else {
      trigger_error($sql." ".mysql_error());  
      return false;    
    }
  }
  
  private function changeParametersGroup($oldGroupId, $newGroupId){
    $sql = "update ".DB_PREF."parameter set `group_id` = ".(int)$newGroupId." where `group_id` = ".(int)$oldGroupId." ";
    $rs = mysql_query($sql);
    if($rs){
      return true;
    } else {
      trigger_error($sql." ".mysql_error());  
      return false;    
    }
  }

  

  
  private function getModuleId($group_name, $module_name){
    $answer = array();
    $sql = "select m.id from `".DB_PREF."module` m, `".DB_PREF."module_group` g 
    where m.`group_id` = g.`id` and g.`name` = '".mysql_real_escape_string($group_name)."' and m.`name` = '".mysql_real_escape_string($module_name)."' ";
    $rs = mysql_query($sql);
    if($rs){
      if($lock = mysql_fetch_assoc($rs)){
        return $lock['id'];
      } else {
        return false;
      }
    } else {
      trigger_error($sql." ".mysql_error());  
      return false;    
    }
    
  }
  
  private function deleteModule($id){
    $sql = "delete from  ".DB_PREF."module where id = '".(int)$id."' ";
    $rs = mysql_query($sql);
    if($rs){
      return true;
    } else {
      //trigger_error($sql." ".mysql_error());  
      return false;    
    }    
  }
  
  private function renameModule($id, $newName, $newTranslation){
    $sql = "update ".DB_PREF."module 
    set `name` = '".mysql_real_escape_string($newName)."', `translation` = '".mysql_real_escape_string($newTranslation)."' 
    where `id` = ".(int)$id." ";
    $rs = mysql_query($sql);
    if($rs){
      return true;
    } else {
      trigger_error($sql." ".mysql_error());  
      return false;    
    }
  }  
  
  private function getWidget($id){
    $sql = "select * from ".DB_PREF."content_module where id = '".mysql_real_escape_string($id)."' ";
    $rs = mysql_query($sql);
    if($rs){
      if($lock = mysql_fetch_assoc($rs)){
        return $lock;
      } else {
        return false;
      }
    } else {
      trigger_error($sql." ".mysql_error());  
      return false;    
    }
  }
  
  private function getWidgetGroup($id){
    $sql = "select * from ".DB_PREF."content_module_group where id = '".mysql_real_escape_string($id)."' ";
    $rs = mysql_query($sql);
    if($rs){
      if($lock = mysql_fetch_assoc($rs)){
        return $lock;
      } else {
        return false;
      }
    } else {
      trigger_error($sql." ".mysql_error());  
      return false;    
    }
  }

  private function getParametersGroup($moduleId, $name){
    $sql = "select * from ".DB_PREF."parameter_group where `module_id` = '".mysql_real_escape_string($moduleId)."' and `name` = '".mysql_real_escape_string($name)."' ";
    $rs = mysql_query($sql);
    if($rs){
      if($lock = mysql_fetch_assoc($rs)){
        return $lock;
      } else {
        return false;
      }
    } else {
      trigger_error($sql." ".mysql_error());  
      return false;    
    }
  }
  
  
  private function removeContentModuleColumn(){
    $sql = "alter table `".DB_PREF."parameter_group` drop `content_module_id`";
    $rs = mysql_query($sql);
    if($rs){
      return true;
    } else {
      //trigger_error($sql." ".mysql_error());  
      return false;    
    }
  }        
  
  private function addEmptyTranslations($name, $translation){
    $sql = "update ".DB_PREF."parameter 
    set `translation` = '".mysql_real_escape_string($translation)."' 
    where `name` = '".mysql_real_escape_string($name)."' and `translation` = '' ";
    $rs = mysql_query($sql);
    if($rs){
      return true;
    } else {
      trigger_error($sql." ".mysql_error());  
      return false;    
    }
  }  
  
}


