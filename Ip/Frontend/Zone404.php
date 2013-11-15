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
        return array($this->getCurrentPage());
    }

    public function getElement($elementId) {
        return false;
    }

    
    public function findElement($urlVars, $getVars) {
        return new Element(null, $this->getName());
    }

    public function getCurrentPage()
    {
        return new Page404(1, $this->getName());
    }
    
    public function getLayout() {
        return is_file(\Ip\Config::themeFile('404.php')) ? '404.php' : 'main.php';
    }
}