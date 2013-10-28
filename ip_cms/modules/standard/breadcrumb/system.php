<?php
/**
 * @package ImpressPages

 *
 */
namespace Modules\standard\breadcrumb;
if (!defined('CMS')) exit;



class System{



    function init(){
        global $dispatcher;
        $dispatcher->bind('site.generateBlock', __NAMESPACE__ .'\System::generateBreadcrumb');
        $dispatcher->bind('site.generateSlot', __NAMESPACE__ .'\System::generateBreadcrumbSlot');
    }


    
    public static function generateBreadcrumb (\Ip\Event $event) {
        $blockName = $event->getValue('blockName');
        if ($blockName == 'ipBreadcrumb') {
            require_once \Ip\Config::oldModuleFile('standard/breadcrumb/module.php');
            $event->setValue('content', \Modules\standard\breadcrumb\Module::generateBreadcrumb(' &rsaquo; ') );
            $event->addProcessed();
        }
    }


    public static function generateBreadcrumbSlot (\Ip\Event $event) {
        $name = $event->getValue('slotName');
        if ($name == 'ipBreadcrumb') {
            require_once \Ip\Config::oldModuleFile('standard/breadcrumb/module.php');
            $event->setValue('content', \Modules\standard\breadcrumb\Module::generateBreadcrumb(' &rsaquo; ') );
            $event->addProcessed();
        }
    }

}