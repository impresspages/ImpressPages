<?php
/**
 * @package	ImpressPages
 * @copyright	Copyright (C) 2011 ImpressPages LTD.
 * @license	GNU/GPL, see ip_license.html
 */
namespace Modules\standard\content_management;

class Widget{
    var $name;

    public function __construct($name) {
        $this->name = $name;
    }
    
    public function getName () {
        return $this->name;
    }
    

}