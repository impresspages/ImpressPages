<?php
/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2011 ImpressPages LTD.
 * @license GNU/GPL, see ip_license.html
 */
namespace Modules\standard\content_management;
if (!defined('CMS')) exit;


require_once(__DIR__.'/model.php');

class System{

    

    function init(){
        global $site;
        global $dispatcher;
        if ($site->managementState()) {
            $site->addJavascript(BASE_URL.LIBRARY_DIR.'js/jquery/ui/jquery-ui.js');
            $site->addJavascript(BASE_URL.MODULE_DIR.'standard/content_management/public/ipContentManagement.js');
            $site->addJavascript(BASE_URL.MODULE_DIR.'standard/content_management/public/jquery.ip.contentManagement.js');
            $site->addJavascript(BASE_URL.MODULE_DIR.'standard/content_management/public/jquery.ip.widgetbutton.js');
            $site->addJavascript(BASE_URL.MODULE_DIR.'standard/content_management/public/jquery.ip.block.js');
            $site->addJavascript(BASE_URL.MODULE_DIR.'standard/content_management/public/jquery.ip.widget.js');
            
            $site->addJavascript(BASE_URL.LIBRARY_DIR.'js/ui/jquery-ui.js');
            $site->addCss(BASE_URL.LIBRARY_DIR.'js/ui/jquery-ui.css');

            $site->addJavascript(BASE_URL.LIBRARY_DIR.'js/plupload/plupload.full.js');
            $site->addJavascript(BASE_URL.LIBRARY_DIR.'js/plupload/plupload.browserplus.js');
            $site->addJavascript(BASE_URL.LIBRARY_DIR.'js/plupload/plupload.gears.js');
            
            
            
            $getVars = array (
				'g' => 'standard',
				'm' => 'content_management',
				'a' => 'initVariables'            
            );
            $site->addJavascript($site->generateUrl(null, null, null, $getVars));
            $site->addCss(BASE_URL.MODULE_DIR.'standard/content_management/public/widgets.css');
            
            
            foreach (self::_getWidgets() as $key => $widgetName) {
                $site->addJavascript(BASE_URL.MODULE_DIR.'standard/content_management/widget/'.$widgetName.'/'.$widgetName.'.js');
            }
            
        }     

	    $dispatcher->bind('contentManagement.collectWidgets', __NAMESPACE__ .'\System::collectWidgets');

        $dispatcher->bind('site.duplicatedRevision', __NAMESPACE__ .'\System::duplicatedRevision');
	    
    }
    
    public static function collectWidgets(EventWidget $event){
        
        foreach (self::_getWidgets() as $key => $widgetName) {
            require_once(BASE_DIR.MODULE_DIR.'standard/content_management/widget/'.$widgetName.'/'.$widgetName.'.php');
            
            eval('$widget = new \Modules\standard\content_management\Widget_'.$widgetName.'();');
            $event->addWidget($widget);
        }
        
//        require_once(BASE_DIR.MODULE_DIR.'standard/content_management/widget/title/widget.php');
//        require_once(BASE_DIR.MODULE_DIR.'standard/content_management/widget/text/widget.php');
//        require_once(BASE_DIR.MODULE_DIR.'standard/content_management/widget/text_photo/widget.php');
//        
//        $widget = new WidgetTitle();
//        $event->addWidget($widget);
//        $widget = new WidgetText();
//        $event->addWidget($widget);
//        $widget = new WidgetTextPhoto();
//        $event->addWidget($widget);
    }
    
    public static function duplicatedRevision (\Ip\Event $event) {
        Model::duplicateRevision($event->getValue('basedOn'), $event->getValue('newRevisionId'));
    }   
    
    
    private static function _getWidgets () {
        return array (
            'ipTitle',
            'ipText',
            'ipTextPhoto',            
        );           
    }

    
}            
        

