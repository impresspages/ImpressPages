<?php
/**
 * @package   ImpressPages
 * @copyright Copyright (C) 2009 JSC Apro media.
 * @license   GNU/GPL, see license.html
 */
namespace Modules\shop\currencies;

if (!defined('BACKEND')) exit;

require_once(BASE_DIR.MODULE_DIR.'developer/std_mod/std_mod.php');

class CurrenciesArea extends \Modules\developer\std_mod\Area{
  private $errors;   
  
  function __construct(){
    global $parametersMod;
      
    parent::__construct(
      array(
      'title' => $parametersMod->getValue('shop', 'currencies', 'admin_translations', 'currencies'),
      'dbTable' => 'm_shop_currency',
      'dbPrimaryKey' => 'id',
      'searchable' => true,
      'sortable' => true,
      'newRecordPosition' => 'null',
      'sortField' => 'row_number',      
      'sortType' => 'pointer'      
      )    
    );

    $errors = array();
    
    $element = new \Modules\developer\std_mod\ElementText(
    array(     
      'title' => $parametersMod->getValue('shop', 'currencies', 'admin_translations', 'title'),
      'dbField' => 'title',
      'showOnList' => true,
      'searchable' => true
    )
    );
    $this->addElement($element);
        
    $element = new \Modules\developer\std_mod\ElementText(
    array(     
      'title' => $parametersMod->getValue('shop', 'currencies', 'admin_translations', 'code'),
      'dbField' => 'code',
      'showOnList' => true,
      'searchable' => true,
      'regExpression' => '/[A-Z][A-Z][A-Z]/',
      'regExpressionForUser' => 'Languae code should contain three capital letters'
    )
    );
    $this->addElement($element);
    
      
    $element = new \Modules\developer\std_mod\ElementNumber(
    array(     
      'title' => $parametersMod->getValue('shop', 'currencies', 'admin_translations', 'rate'),
      'dbField' => 'rate',
      'showOnList' => true,
      'searchable' => true,
      'regExpression' => $parametersMod->getValue('developer','std_mod','parameters','number_real_reg_expression'),
      'regExpressionError' => $parametersMod->getValue('developer','std_mod','admin_translations','error_number')
    )
    );
    $this->addElement($element);    
    
    $element = new \Modules\developer\std_mod\ElementBool(
    array(     
      'title' => $parametersMod->getValue('shop', 'currencies', 'admin_translations', 'default'),
      'dbField' => 'default',
      'showOnList' => true,
      'searchable' => true
    )
    );
    $this->addElement($element);    
    
  }
   
  public function allowDelete($recordId){
    $this->errors['delete'] = $parametersMod->getValue('shop', 'currencies', 'admin_translations', 'error_default_delete');
    return false;    
  }
  
  public function lastError($action){
    if(isset($this->errors[$action])){
      return $this->errors[$action];
    }
  }
  
  public function afterInsert($recordId){
    require_once(__DIR__.'/db.php');
    $record = (Db::getCurrencyById($recordId));
    if($record['default']){
      Db::setDefault($recordId);
    }
  }
  
  public function afterUpdate($recordId){
    $this->afterInsert($recordId);
  }
  



    
  
}