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

    protected $page;

    public function getPages($language = null, $parentPageId = null, $startFrom = 0, $limit = null, $includeHidden = false, $reverseOrder = false){
        return array($this->getCurrentPage());
    }

    public function getPage($pageId) {
        return $this->page();
    }

    
    public function findPage($urlVars, $getVars) {
        return $this->page();
    }

    public function getCurrentPage()
    {
        return $this->page();
    }
    
    public function getLayout() {
        return is_file(ipThemeFile('404.php')) ? '404.php' : 'main.php';
    }

    private function page() {
        if ($this->page === null) {
            $this->page = new Page404();
        }
        return $this->page;
    }
}
