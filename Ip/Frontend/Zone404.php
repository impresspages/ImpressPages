<?php
/**
 * @package ImpressPages
 *
 *
 */
namespace Ip\Frontend;

use Ip\Page;

class Zone404 extends \Ip\Frontend\Zone {
    
    
    public function getPages($language = null, $parentElementId = null, $startFrom = 0, $limit = null, $includeHidden = false, $reverseOrder = false){
        return array($this->getCurrentPage());
    }

    public function getPage($pageId) {
        return false;
    }

    
    public function findPage($urlVars, $getVars) {
        return new Page(null, $this->getName());
    }

    public function getCurrentPage()
    {
        return new Page404(1, $this->getName());
    }
    
    public function getLayout() {
        return is_file(\Ip\Config::themeFile('404.php')) ? '404.php' : 'main.php';
    }
}