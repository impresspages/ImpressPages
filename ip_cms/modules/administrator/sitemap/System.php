<?php
/**
 * @package ImpressPages
 *
 */
namespace Modules\administrator\sitemap;



class System{



    function init(){
        global $dispatcher;

        $dispatcher->bind('site.generateBlock', array($this, 'catchGenerateBlock'));
    }


    public function catchGenerateBlock (\Ip\Event $event) {
        $service = Service::instance();
        $blockName = $event->getValue('blockName');
        if ( $blockName == 'ipSitemap' ) {

            $event->setValue('content', $service->generateSitemapIcon());
            $event->addProcessed();
        }
    
        
    
    }    

}