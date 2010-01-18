<?php
/**
 * @package   ImpressPages
 * @copyright Copyright (C) 2009 JSC Apro media.
 * @license   GNU/GPL, see license.html
 */
namespace Modules\catalog\categories;

if (!defined('BACKEND')) exit;

require_once(BASE_DIR.PLUGIN_DIR.'catalog/categories/categories_area.php');

class Manager{
  var $standardModule;
   


  function __construct() {
    global $parametersMod;

    //$categoriesArea = new \Modules\developer\std_mod\Area(
    $categoriesArea = new CategoriesArea();
     
    $subcategoriesArea = new CategoriesArea();
    

    $subcategoriesArea->whereCondition = null;
    $subcategoriesArea->addArea($subcategoriesArea);
    
    $categoriesArea->addArea($subcategoriesArea);
    
    $this->standardModule = new \Modules\developer\std_mod\StandardModule($categoriesArea);
  }

  
  
  function manage() {
    return $this->standardModule->manage();
  }
}
