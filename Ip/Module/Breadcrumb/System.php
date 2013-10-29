<?php
/**
 * @package ImpressPages

 *
 */
namespace Ip\Module\Breadcrumb;



class System{



    function init(){
        $dispatcher = \Ip\ServiceLocator::getDispatcher();
        $dispatcher->bind('site.generateBlock', __NAMESPACE__ .'\System::generateBreadcrumb');
        $dispatcher->bind('site.generateSlot', __NAMESPACE__ .'\System::generateBreadcrumbSlot');
    }


    
    public static function generateBreadcrumb (\Ip\Event $event) {
        $blockName = $event->getValue('blockName');
        if ($blockName == 'ipBreadcrumb') {
            $event->setValue('content', Module::generateBreadcrumb(' &rsaquo; ') );
            $event->addProcessed();
        }
    }


    public static function generateBreadcrumbSlot (\Ip\Event $event) {
        $name = $event->getValue('slotName');
        if ($name == 'ipBreadcrumb') {
            $event->setValue('content', Module::generateBreadcrumb(' &rsaquo; ') );
            $event->addProcessed();
        }
    }

}