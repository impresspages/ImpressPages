<?php
/**
 * @package ImpressPages

 *
 */
namespace Modules\administrator\search;
if (!defined('CMS')) exit;



class System{



    function init(){
        global $dispatcher;

        $dispatcher->bind('site.generateBlock', __NAMESPACE__ .'\System::generateContent');
        $dispatcher->bind('site.generateBlock', __NAMESPACE__ .'\System::generateSearchBox');
        $dispatcher->bind('site.generateSlot', __NAMESPACE__ .'\System::generateSlot');
    }


    public static function generateContent (\Ip\Event $event) {
        global $site;
        $blockName = $event->getValue('blockName');
        if (
            $blockName != 'main' ||
            !$site->getCurrentZone() ||
            $site->getCurrentZone()->getAssociatedModule() != 'search' ||
            $site->getCurrentZone()->getAssociatedModuleGroup() != 'administrator'
        ) {
            return;
        }
        
        $event->setValue('content', $site->getCurrentElement()->generateContent());
        $event->addProcessed();
        
    }


    public static function generateSearchBox (\Ip\Event $event) {
        $site = \Ip\ServiceLocator::getSite();
        $blockName = $event->getValue('blockName');
        if ( $blockName == 'ipSearch' ) {
            $searchZone = $newsletterBox = $site->getZoneByModule('administrator', 'search');
            if (!$searchZone) {
                return;
            }
            
            $event->setValue('content', $searchZone->generateSearchBox());
            $event->addProcessed();
        }
    }

    public static function generateSlot (\Ip\Event $event) {
        $site = \Ip\ServiceLocator::getSite();
        $name = $event->getValue('slotName');
        if ( $name == 'ipSearch' ) {
            $searchZone = $newsletterBox = $site->getZoneByModule('administrator', 'search');
            if (!$searchZone) {
                return;
            }

            $event->setValue('content', $searchZone->generateSearchBox());
            $event->addProcessed();
        }
    }

}