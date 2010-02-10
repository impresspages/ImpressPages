<?php
/**
 * @package		Library
 * @copyright	Copyright (C) 2009 JSC Apro media.
 * @license		GNU/GPL, see license.html
 */
namespace Modules\developer\std_mod;
 
if (!defined('BACKEND')) exit;

class ElementPswd extends Element{ //data element in area
  var $hashSalt = '';
  var $useHash = true;

  function __construct($variables){    
    parent::__construct($variables);

      
    if(!isset($variables['dbField']) || $variables['dbField'] == ''){
      $backtrace = debug_backtrace();
      if(isset($backtrace[0]['file']) && $backtrace[0]['line'])
        trigger_error('ElementPass dbField parameter not set. (Error source: '.$backtrace[0]['file'].' line: '.$backtrace[0]['line'].' ) ');
      else
        trigger_error('ElementPass dbField parameter not set.');
      exit;
    }
    
    if(isset($variables['sortable']) && $variables['sortable'] == true){
      $backtrace = debug_backtrace();
      if(isset($backtrace[0]['file']) && $backtrace[0]['line'])
        trigger_error('ElementPass can\'t be sortable. (Error source: '.$backtrace[0]['file'].' line: '.$backtrace[0]['line'].' ) ');
      else
        trigger_error('ElementPass can\'t be sortable.');
      exit;
    }    
        
    foreach ($variables as $name => $value) {
      switch ($name){
        case 'regExpression': 
          $this->regExpression = $value;
        break;
        case 'maxLength': 
          $this->maxLength = $value;
        break;
        case 'dbField': 
          $this->dbField = $value;
        break;      
        case 'hashSalt': 
          $this->dbField = $value;
        break;      
        case 'useHash': 
          $this->dbField = $value;
        break;      
      }
   }
    
  }   
  
  function printFieldNew($prefix, $parentId, $area){
    
    $html = new StdModHtmlOutput();
    $html->inputPassword($prefix, $this->defaultValue, $this->defaultValue, $this->disabledOnInsert);
    return $html->html;

  }

  function printFieldUpdate($prefix, $record, $area){


    $html = new StdModHtmlOutput();
    $html->inputPassword($prefix, $this->defaultValue, $this->defaultValue, $this->disabledOnUpdate);
    return $html->html;

         
  }


  function getParameters($action, $prefix, $area){
    if($this->visibleOnInsert && !$this->disabledOnInsert && $action == 'insert'){
      if (isset($_REQUEST[''.$prefix]) && $_REQUEST[''.$prefix] != ""){
        if($this->useHash){
          $tmpPassword = md5($_REQUEST[''.$prefix].$this->hashSalt);
        } else {
          $tmpPassword = $_REQUEST[''.$prefix];
        }
        return array("name"=>$this->dbField, "value"=>$tmpPassword);
      }else
       return false;
    }
        
    if($this->visibleOnUpdate && !$this->disabledOnUpdate && $action == 'update'){
      if($this->useHash){
        $value = md5($_REQUEST[''.$prefix].$this->hashSalt);
      } else {
        $value = $_REQUEST[''.$prefix];
      }

      return array("name"=>$this->dbField, "value"=>$value);
    }
    
    
  
  }


  function previewValue($record, $area){
    require_once(BASE_DIR.LIBRARY_DIR.'php/text/string.php');
    $answer = mb_substr($record[$this->dbField], 0, $this->previewLength);
    $answer = htmlspecialchars($answer);
    $answer = \Library\Php\Text\String::mb_wordwrap($answer, 10, "&#x200B;", 1);    
    return $answer;    
  }

  function checkField($prefix, $action, $area){
    global $parametersMod;
    if($action != 'update' || !$this->disabledOnUpdate && $this->visibleOnUpdate){
      if($action == 'insert' && $this->disabledOnInsert || $action == 'insert' && !$this->visibleOnInsert){
        $_REQUEST[''.$prefix] = $this->defaultValue;
        $_REQUEST[''.$prefix.'_confirm'] = $this->defaultValue;
      }
    
      if ($_REQUEST[''.$prefix] != $_REQUEST[''.$prefix.'_confirm'] )
         return $parametersMod->getValue('developer', 'std_mod', 'admin_translations', 'passwords_do_not_match');
      else
         return null;
    }
    return null;
  }
 
  


  function printSearchField($level, $key, $area){
    if (isset($_REQUEST['search'][$level][$key]))
    $value = $_REQUEST['search'][$level][$key];
    else
    $value = '';
    return '<input name="search['.$level.']['.$key.']" value="'.htmlspecialchars($value).'" />';
  }

  function getFilterOption($value, $area){
    return " ".$this->dbField." like '%".mysql_real_escape_string($value)."%' ";
  }
  


}

