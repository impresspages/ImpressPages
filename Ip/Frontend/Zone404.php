<?php
/**
 * @package ImpressPages
 *
 *
 */
namespace Ip\Frontend;

use Ip\Frontend\Element;

class Zone404 extends \Ip\Frontend\Zone {
    
    
    public function getElements($language = null, $parentElementId = null, $startFrom = 0, $limit = null, $includeHidden = false, $reverseOrder = false){
        return array();
    }

    public function getElement($elementId) {
        return false;
    }

    
    public function findElement($urlVars, $getVars) {
        return new Element(null, $this->getName());
    }
    
    
    public function getLayout() {
        global $parametersMod;
        return $parametersMod->getValue('standard', 'configuration', 'error_404', 'error_page_template');
    }
}