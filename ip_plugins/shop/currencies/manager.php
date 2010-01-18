<?php
/**
 * @package   ImpressPages
 * @copyright Copyright (C) 2009 JSC Apro media.
 * @license   GNU/GPL, see license.html
 */
namespace Modules\shop\currencies;

if (!defined('BACKEND')) exit;
require_once(BASE_DIR.PLUGIN_DIR.'shop/currencies/currencies_area.php');


class Manager{
  var $standardModule;
   


  function __construct() {

    $currenciesArea = new CurrenciesArea();
    
    $this->standardModule = new \Modules\developer\std_mod\StandardModule($currenciesArea);
  }

 
  function manage() {
    return $this->standardModule->manage();
  }
}
