<?php
/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2011 ImpressPages LTD.
 * @license GNU/GPL, see ip_license.html
 */
namespace Modules\community\newsletter;
if (!defined('CMS')) exit;



class System{



    function init(){
        global $site;
        global $dispatcher;

        $site->addJavascript(BASE_URL.MODULE_DIR.'community/newsletter/newsletter.js');
        
        $dispatcher->bind('site.generateBlock', __NAMESPACE__ .'\System::generateContent');
    }


    public static function generateContent (\Ip\Event $event) {
        global $site;
        $blockName = $event->getValue('blockName');
        if (
            $blockName != 'main' ||
            $site->getCurrentZone()->getAssociatedModule() != 'newsletter' ||
            $site->getCurrentZone()->getAssociatedModuleGroup() != 'community'
        ) {
            return;
        }
        echo $site->getCurrentElement()->generateContent();
        
    }



}