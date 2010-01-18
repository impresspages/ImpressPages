<?php
/**
 * @package   ImpressPages
 * @copyright Copyright (C) 2009 JSC Apro media.
 * @license   GNU/GPL, see license.html
 */
namespace Modules\shop\currencies_to_languages;

if (!defined('BACKEND')) exit;
require_once(BASE_DIR.MODULE_DIR.'developer/std_mod/elements/element_select.php');
require_once(BASE_DIR.PLUGIN_DIR.'shop/currencies/db.php');

class SpecialSelect extends \Modules\developer\std_mod\ElementSelect{
  
  
  function previewValue($record, $area){
    $languageId = $record[$this->dbField];
    
    $currency = \Modules\shop\currencies\Db::getCurrencyByLanguage($languageId);  
    
    $answer = '';
    
    if($currency){
      $answer = $currency['title'];
    }
    
    $answer = mb_substr($answer, 0, $this->previewLength);
    $answer = htmlspecialchars($answer);
    $answer = wordwrap($answer, 10, "&#x200B;", 1);    
    return $answer;
    
    return mb_substr($answer, 0, $this->previewLength);
  }  
  
  function printFieldUpdate($prefix, $record, $area){
    $value = null;
   
    $languageId = $record[$this->dbField];
    
    $currency = \Modules\shop\currencies\Db::getCurrencyByLanguage($languageId);  
    
    if($currency){
      $value = $currency['id'];
    } else {
      $value = null;
    }
    
    $html = new \Modules\developer\std_mod\StdModHtmlOutput();
    $html->inputSelect($prefix, $this->values, $value, $this->disabledOnUpdate);
    return $html->html;
      
    return $answer;  
  }  

  function getParameters($action, $prefix, $area){
    return false;
  }
  
  
  function processUpdate($prefix, $rowId, $area){

    $currency = \Modules\shop\currencies\Db::getCurrencyByLanguage($rowId);  
    
    if($currency){
      $sql = "update `".DB_PREF."m_shop_currency_to_language` set `currency_id` = ".(int)$_REQUEST[$prefix]." where `language_id` = ".(int)$rowId."";
    } else {
      $sql = "insert into `".DB_PREF."m_shop_currency_to_language` set `currency_id` = ".(int)$_REQUEST[$prefix].", `language_id` = ".(int)$rowId."";
    }
    
    $rs = mysql_query($sql);
    if(!$rs){
      trigger_error($sql." ".mysql_error());
    }
    
  }  
  
  
}
   