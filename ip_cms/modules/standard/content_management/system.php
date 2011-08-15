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
            
            $getVars = array (
				'g' => 'standard',
				'm' => 'content_management',
				'a' => 'initVariables'            
            );
            $site->addJavascript($site->generateUrl(null, null, null, $getVars));
            $site->addCss(BASE_URL.MODULE_DIR.'standard/content_management/public/widgets.css');
            
            
            $site->addJavascript(BASE_URL.MODULE_DIR.'standard/content_management/widget/text/ipWidgetText.js');
            $site->addJavascript(BASE_URL.MODULE_DIR.'standard/content_management/widget/title/ipWidgetTitle.js');
            
        }     

	    $dispatcher->bind('contentManagement.collectWidgets', __NAMESPACE__ .'\System::collectWidgets');

        $dispatcher->bind('site.duplicatedRevision', __NAMESPACE__ .'\System::duplicatedRevision');
	    
    }
    
    public static function collectWidgets(EventWidget $event){
        $widgets = array (
            'Title',
            'Text',
            'TextPhoto',
        ); 
        
        
        
        require_once(BASE_DIR.MODULE_DIR.'standard/content_management/widget/title/widget.php');
        require_once(BASE_DIR.MODULE_DIR.'standard/content_management/widget/text/widget.php');
        
        $widget = new WidgetTitle();
        $event->addWidget($widget);
        $widget = new WidgetText();
        $event->addWidget($widget);
    }
    
    public static function duplicatedRevision (\Ip\Event $event) {
        Model::duplicateRevision($event->getValue('basedOn'), $event->getValue('newRevisionId'));
    }   
    

    
}            
        

