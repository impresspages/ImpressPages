<?php
/**
 * @package   ImpressPages
 * @copyright Copyright (C) 2009 JSC Apro media.
 * @license   GNU/GPL, see license.html
 */
namespace Modules\shop\currencies_to_languages;

if (!defined('BACKEND')) exit;

require_once(BASE_DIR.MODULE_DIR.'developer/std_mod/std_mod.php');
require_once(__DIR__.'/element_special_select.php');

class LanguagesArea extends \Modules\developer\std_mod\Area{
  private $errors;   
  
  function __construct(){
    global $parametersMod;
      
    parent::__construct(
      array(
      'title' => $parametersMod->getValue('shop', 'currencies_to_languages', 'admin_translations', 'currencies_to_languages'),
      'dbTable' => 'language',
      'dbPrimaryKey' => 'id',
      'allowInsert' => false,
      'allowDelete' => false,
      'orderBy' => 'row_number'            
      )    
    );

    $errors = array();
    
    $element = new \Modules\developer\std_mod\ElementText(
    array(     
      'title' => $parametersMod->getValue('shop', 'currencies_to_languages', 'admin_translations', 'language'),
      'dbField' => 'd_long',
      'showOnList' => true,
      'disabledOnUpdate' => true,
      'order' => false
    )
    );
    $this->addElement($element);

    
    $element = new SpecialSelect(
    array(     
      'title' => $parametersMod->getValue('shop', 'currencies_to_languages', 'admin_translations', 'associated_currency'),
      'dbField' => 'id',
      'showOnList' => true,
      'values' => $this->getCurrencies()
    )
    );
    $this->addElement($element);    
    
  }

 
  private function getLanguages(){
    require_once(BASE_DIR.MODULE_DIR.'standard/languages/db.php');
    
    $languages = \Modules\standard\languages\Db::getLanguages();
    $answer[] = array(null, '');
    
    foreach($languages  as $language){
      $answer[] = array($language['id'], $language['d_long']);
    }
    return $answer;
  }    

  private function getCurrencies(){
    require_once(BASE_DIR.PLUGIN_DIR.'shop/currencies/db.php');
    
    $currencies = \Modules\shop\currencies\Db::getCurrencies();
    $answer[] = array(null, '');
    
    foreach($currencies  as $currency){
      $answer[] = array($currency['id'], $currency['title']);
    }
    return $answer;
  }    
  
}