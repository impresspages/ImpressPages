<?php
/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2011 ImpressPages LTD.
 * @license GNU/GPL, see ip_license.html
 */
namespace Modules\standard\content_management;
if (!defined('CMS')) exit;


class System{

    function init(){
        global $site;
        global $dispatcher;
        if ($site->managementState()) {
            $site->addJavascript(BASE_URL.LIBRARY_DIR.'js/jquery/ui/jquery-ui.js');
            $site->addJavascript(BASE_URL.MODULE_DIR.'standard/content_management/content_management.js');
            $site->addJavascript(BASE_URL.MODULE_DIR.'standard/content_management/jquery/jquery.ip.widgetbutton.js');
            $site->addJavascript(BASE_URL.MODULE_DIR.'standard/content_management/jquery/jquery.ip.block.js');
            $actionData = array (
				'g' => 'standard',
				'm' => 'content_management',
				'a' => 'initVariables'            
            );
            $site->addJavascript($site->generateUrl(null, null, null, $actionData));
            $site->addCss(BASE_URL.MODULE_DIR.'standard/content_management/content_management.css');
        }     

	    $dispatcher->bind('contentManagement.collectWidgets', __NAMESPACE__ .'\System::collectWidgets');
        
    }
    
    public static function collectWidgets(\Ip\Event $event){
        require_once(BASE_DIR.MODULE_DIR.'standard/content_management/widget.php');
        
        
        $widgets = $event->getValue('widgets');
        $widget = new Widget('title', 'Title');
        $widgets[] = $widget;
        $widget = new Widget('text', 'Text');
        $widgets[] = $widget;
        $event->setValue('widgets', $widgets);
        
    }
    

    
}            
        

