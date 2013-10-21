<?php
/**
 * @package ImpressPages
 *
 *
 */
namespace Modules\administrator\rss;
if (!defined('FRONTEND')&&!defined('BACKEND')) exit;




require_once (__DIR__.'/db.php');

class System{

    function clearCache($cachedBaseUrl){
        Db::clearCache();
    }

    
    public function init() {
        global $site;
        
        $curZone = $site->getCurrentZone();
        if ($curZone && $curZone->getAssociatedModuleGroup() == 'administrator' && $curZone->getAssociatedModule() == 'rss') {
            $site->setOutput($site->getCurrentElement()->generateContent() );
        }
    }
    
    
}