<?php

namespace Modules\developer\widgets;

if (!defined('BACKEND')) exit;  //this file can't be acessed directly

require_once(__DIR__.'/items_area.php');

class Manager{
    var $standardModule;
     
    function __construct() {
        $itemsArea = new ItemsArea();
        $this->standardModule = new \Modules\developer\std_mod\StandardModule($itemsArea);
    }

    function manage() {
        Model::recreateWidgetsList();
        return $this->standardModule->manage();
    }
    
    
}