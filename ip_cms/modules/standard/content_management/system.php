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
            
            $site->addJavascript(BASE_URL.MODULE_DIR.'developer/upload/jquery.ip.uploadPicture.js');
            
            $site->addCss(BASE_URL.MODULE_DIR.'standard/content_management/public/widgets.css');
            
        }     

	    $dispatcher->bind('contentManagement.collectWidgets', __NAMESPACE__ .'\System::collectWidgets');

        $dispatcher->bind('site.duplicatedRevision', __NAMESPACE__ .'\System::duplicatedRevision');
	    
    }
    
    public static function collectWidgets(EventWidget $event){
        global $site;
        require_once(BASE_DIR.FRONTEND_DIR.'db.php');
        $modules = \Frontend\Db::getModules();

         
        //loopa all installed modules
        foreach ($modules as $moduleKey => $module) {
                $themeDir = BASE_DIR.THEME_DIR.THEME.'/modules/'.$module['g_name'].'/'.$module['m_name'].'/widget/';
                
                if ($module['core']) {
                    $widgetDir = BASE_DIR.MODULE_DIR.$module['g_name'].'/'.$module['m_name'].'/widget/';
                } else {
                    $widgetDir = BASE_DIR.PLUGIN_DIR.$module['g_name'].'/'.$module['m_name'].'/widget/';
                }
                       
                if (! file_exists($widgetDir) || ! is_dir($widgetDir)) {
                    continue;
                }
                
                $widgetFolders = scandir($widgetDir);
                
                if ($widgetFolders === false) {
                    return;
                }
                
                //foeach all widget folders
                foreach ($widgetFolders as $widgetFolderKey => $widgetFolder) {
                    //each directory is a widget  
                    if (!is_dir($widgetDir.$widgetFolder)){
                        continue;
                    }            
                    
                    //register widget if widget controller exists
                    if (file_exists($widgetDir.$widgetFolder.'/'.$widgetFolder.'.php') && is_file($widgetDir.$widgetFolder.'/'.$widgetFolder.'.php')) {
                        require_once($widgetDir.$widgetFolder.'/'.$widgetFolder.'.php');
                        eval('$widget = new \\Modules\\'.$module['g_name'].'\\'.$module['m_name'].'\\widget\\'.$widgetFolder.'();');
                        $event->addWidget($widget);                    
                    }
                    
                    //scan for js and css files required for widget management
                    if ($site->managementState()) {
                        self::includeResources($widgetDir.$widgetFolder, $themeDir.$widgetFolder);
                    }
                    $publicResourcesDir = $widgetDir.$widgetFolder.'/public';
                    $publicResourcesThemeDir = $themeDir.$widgetFolder.'/public';
                    if (file_exists($publicResourcesDir) && is_dir($publicResourcesDir)){
                        self::includeResources($publicResourcesDir, $publicResourcesThemeDir);
                    }
                }
        }

    }

    public static function includeResources($resourcesFolder, $overrideFolder){
        global $site;
        $files = scandir($resourcesFolder);
        if ($files === false) {
            continue;
        }
        foreach ($files as $fileKey => $file) {
            if (is_dir($resourcesFolder)){
                continue;
            }      
            if (substr($file, -3) == '.js'){
                $site->addJavascript($resourcesFolder.'/'.$file);
            }
            if (substr($file, -4) == '.css'){
                //overriden css version exists
                if (file_exists($overrideFolder.'/'.$file)){
                    $site->addCss($overrideFolder.'/'.$file);
                } else {
                    $site->addCss($resourcesFolder.'/'.$file);
                }
            }
        }
    }
    
    public static function duplicatedRevision (\Ip\Event $event) {
        Model::duplicateRevision($event->getValue('basedOn'), $event->getValue('newRevisionId'));
    }   
    

    
}            
        

