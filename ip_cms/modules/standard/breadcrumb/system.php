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
    }


    
    public static function generateBreadcrumb (\Ip\Event $event) {
        global $site;
        $blockName = $event->getValue('blockName');
        if ($blockName == 'ipBreadcrumb') {
            require_once (BASE_DIR.MODULE_DIR.'standard/breadcrumb/module.php');
            $event->setValue('content', \Modules\standard\breadcrumb\Module::generateBreadcrumb(' &rsaquo; ') );
            $event->addProcessed();
        }
    }    



}