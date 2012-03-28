<?php
/**
 * @package	ImpressPages
 * @copyright	Copyright (C) 2011 ImpressPages LTD.
 * @license see ip_license.html
 */
namespace Frontend;

if (!defined('FRONTEND')&&!defined('BACKEND')) exit;


require_once('element.php');


class Zone404 extends \Frontend\Zone {
    
    
    public function getElements($language = null, $parentElementId = null, $startFrom = 0, $limit = null, $includeHidden = false, $reverseOrder = false){
        return array();
    }

    public function getElement($elementId) {
        return false;
    }

    
    public function findElement($urlVars, $getVars) {
        return false;
    }
    
    
    public function getLayout() {
        global $parametersMod;
        return $parametersMod->getValue('standard', 'configuration', 'error_404', 'error_page_template');
    }
}