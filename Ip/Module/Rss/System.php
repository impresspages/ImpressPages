<?php
/**
 * @package ImpressPages
 *
 *
 */
namespace Ip\Module\Rss;


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