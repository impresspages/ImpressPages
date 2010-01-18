<?php
/**
 * @package   ImpressPages
 * @copyright Copyright (C) 2009 JSC Apro media.
 * @license   GNU/GPL, see license.html
 */
namespace Modules\shop\currencies_to_languages;

if (!defined('BACKEND')) exit;


class Manager{
  var $standardModule;
   


  function __construct() {
    require_once(__DIR__.'/languages_area.php');
    $languagesArea = new LanguagesArea();

        $this->standardModule = new \Modules\developer\std_mod\StandardModule($languagesArea);
  }

 
  function manage() {
    return $this->standardModule->manage();
  }
}
