<?php
/**
 * @package   ImpressPages
 * @copyright Copyright (C) 2009 JSC Apro media.
 * @license   GNU/GPL, see license.html
 */
namespace Modules\catalog\items;

if (!defined('BACKEND')) exit;
require_once(BASE_DIR.PLUGIN_DIR.'catalog/items/items_area.php');
require_once(BASE_DIR.PLUGIN_DIR.'catalog/items/photos_area.php');


class Manager{
  var $standardModule;
   
  function __construct() {
    global $parametersMod;

    
    $itemsArea = new ItemsArea();
   
    $photosArea = new PhotosArea();

    $itemsArea->addArea($photosArea);
    
    $this->standardModule = new \Modules\developer\std_mod\StandardModule($itemsArea);
  }

 
  function manage() {
    return $this->standardModule->manage();
  }
  


  
}
