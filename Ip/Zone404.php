<?php
/**
 * @package ImpressPages
 *
 *
 */
namespace Ip;

use Ip\Page404;
use Ip\Page;

class Zone404 extends \Ip\Zone {
    
    
    public function getPages($language = null, $parentPageId = null, $startFrom = 0, $limit = null, $includeHidden = false, $reverseOrder = false){
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
        return is_file(ipGetConfig()->themeFile('404.php')) ? '404.php' : 'main.php';
    }
}