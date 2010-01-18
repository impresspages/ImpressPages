<?php
/**
 * @package   ImpressPages
 * @copyright Copyright (C) 2009 JSC Apro media.
 * @license   GNU/GPL, see ip_license.html
 */
namespace Modules\catalog\categories;

if (!defined('CMS')) exit;

class ElementSelectCategory extends \Modules\developer\std_mod\ElementSelect{
  function __construct($variables){
    global $site;

    if(!isset($variables['values'])){
      $variables['values'] = $this->getCategories($site->currentLanguage['id']);
    }
    
    
    parent::__construct($variables);
    
  } 
  
  function previewValue($record, $area){
    require_once(BASE_DIR.PLUGIN_DIR.'catalog/categories/db.php');
    global $site;
    
    $category = \Modules\catalog\categories\Db::getCategoryById($site->currentLanguage['id'], $record[$this->dbField]);

    $answer = $category['page_title'];
    
    $answer = mb_substr($answer, 0, $this->previewLength);
    $answer = htmlspecialchars($answer);
    $answer = wordwrap($answer, 10, "&#x200B;", 1);    
    return $answer;
    
  }
  
  function printFieldUpdate($prefix, $record, $area){
    global $site;
    $value = null;
    
    $this->values = $this->getCategories($site->currentLanguage['id'], null, '', $record['id']);
    
   
    $answer = parent::printFieldUpdate($prefix, $record, $area);
      
    return $answer;  
  }
   
  
  private function getCategories($languageId, $parentId = null, $prefix = '', $excludeId = null){
    
    $categories = \Modules\catalog\categories\Db::getCategories($languageId, $parentId);
    $answer = array();
    if($parentId === null){
      $answer[] = array(null, '');
    }
    
    foreach($categories as $category){
      if($excludeId !== null && $excludeId != $category['id']){
        $answer[] = array($category['id'], $prefix.$category['button_title']);
        $answer = array_merge($answer, $this->getCategories($languageId, $category['id'], $prefix.'-', $excludeId));
      }
    }
    return $answer;
  }     
  
  
/*  function checkField($prefix, $action, $area){
    global $parametersMod;
    
    $error = parent::checkField($prefix, $action, $area);
    
    
    
    if($action != 'update' || !$this->disabledOnUpdate && $this->visibleOnUpdate){
      if($action == 'insert' && $this->disabledOnInsert || $action == 'insert' && !$this->visibleOnInsert)
        $_POST[$prefix] = $this->defaultValue;
    
      if ($this->required && (!isset($_POST[$prefix]) || $_POST[$prefix] == ''))
        return $std_par = $parametersMod->getValue('developer', 'std_mod','admin_translations','error_required');
    }
  }*/
  

}