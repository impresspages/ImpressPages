<?php
/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2011 ImpressPages LTD.
 * @license GNU/GPL, see ip_license.html
 */
namespace Modules\administrator\search;
if (!defined('CMS')) exit;



class System{



    function init(){
        global $dispatcher;

        $dispatcher->bind('site.generateBlock', __NAMESPACE__ .'\System::generateContent');
    }


    public static function generateContent (\Ip\Event $event) {
        global $site;
        $blockName = $event->getValue('blockName');
        if (
            $blockName != 'main' ||
            $site->getCurrentZone()->getAssociatedModule() != 'search' ||
            $site->getCurrentZone()->getAssociatedModuleGroup() != 'administrator'
        ) {
            return;
        }
        
        echo $site->getCurrentElement()->generateContent();
        
    }



}