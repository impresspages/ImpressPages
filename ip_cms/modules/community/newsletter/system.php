<?php
/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2011 ImpressPages LTD.
 *
 */
namespace Modules\community\newsletter;
if (!defined('CMS')) exit;



class System{



    function init(){
        global $site;
        global $dispatcher;

        $site->addJavascript(BASE_URL.LIBRARY_DIR.'js/jquery/jquery.js');
        $site->addJavascript(BASE_URL.MODULE_DIR.'community/newsletter/public/newsletter.js');
        
        $dispatcher->bind('site.generateBlock', __NAMESPACE__ .'\System::generateContent');

        $dispatcher->bind('site.generateBlock', __NAMESPACE__ .'\System::generateNewsletter');
        
    }


    public static function generateContent (\Ip\Event $event) {
        global $site;
        
        $blockName = $event->getValue('blockName');
        if (
            $blockName != 'main' ||
            $site->getCurrentZone()->getAssociatedModuleGroup() != 'community' ||
            $site->getCurrentZone()->getAssociatedModule() != 'newsletter' 
        ) {
            return;
        }
        $event->setValue('content', $site->getCurrentElement()->generateContent());
        $event->addProcessed();
    }
    
    
    public static function generateNewsletter (\Ip\Event $event) {
        global $site;
        $blockName = $event->getValue('blockName');
        if ($blockName == 'ipNewsletter') {
            $newsletterZone = $newsletterBox = $site->getZoneByModule('community', 'newsletter');
            if (!$newsletterZone) {
                return;
            }
            $newsletterBox = \Ip\View::create('view/registration_box.php');
            $event->setValue('content', $newsletterBox );
            $event->addProcessed();
        }
    }    



}