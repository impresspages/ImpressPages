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

            $site->addJavascript(BASE_URL.LIBRARY_DIR.'js/tiny_mce/jquery.tinymce.js');

            $site->addJavascript(BASE_URL.LIBRARY_DIR.'js/plupload/plupload.full.js');
            $site->addJavascript(BASE_URL.LIBRARY_DIR.'js/plupload/plupload.browserplus.js');
            $site->addJavascript(BASE_URL.LIBRARY_DIR.'js/plupload/plupload.gears.js');

            $site->addJavascript(BASE_URL.MODULE_DIR.'developer/upload/jquery.ip.uploadPicture.js');
            $site->addJavascript(BASE_URL.MODULE_DIR.'developer/upload/jquery.ip.uploadFile.js');

            $getVariables = array (
                'g' => 'standard',
                'm' => 'configuration',
                'a' => 'tinymceConfig'
                );

                $site->addJavascript($site->generateUrl(null, null, array(), $getVariables));

                $site->addCss(BASE_URL.MODULE_DIR.'standard/content_management/public/widgets.css');

        }

        $dispatcher->bind('contentManagement.collectWidgets', __NAMESPACE__ .'\System::collectWidgets');

        $dispatcher->bind('site.duplicatedRevision', __NAMESPACE__ .'\System::duplicatedRevision');
         
    }

    public static function collectWidgets(EventWidget $event){
        global $site;
        require_once(BASE_DIR.FRONTEND_DIR.'db.php');
        require_once(__DIR__.'/widget.php');
        $modules = \Frontend\Db::getModules();

         
        //loop all installed modules
        foreach ($modules as $moduleKey => $module) {
            $themeDir = THEME_DIR.THEME.'/modules/'.$module['g_name'].'/'.$module['m_name'].'/'.Model::WIDGET_DIR.'/';

            if ($module['core']) {
                $widgetDir = MODULE_DIR.$module['g_name'].'/'.$module['m_name'].'/'.Model::WIDGET_DIR.'/';
            } else {
                $widgetDir = PLUGIN_DIR.$module['g_name'].'/'.$module['m_name'].'/'.Model::WIDGET_DIR.'/';
            }
             
            if (! file_exists(BASE_DIR.$widgetDir) || ! is_dir(BASE_DIR.$widgetDir)) {
                continue;
            }

            $widgetFolders = scandir(BASE_DIR.$widgetDir);

            if ($widgetFolders === false) {
                return;
            }

            //foeach all widget folders
            foreach ($widgetFolders as $widgetFolderKey => $widgetFolder) {
                //each directory is a widget
                if (!is_dir(BASE_DIR.$widgetDir.$widgetFolder) || $widgetFolder == '.' || $widgetFolder == '..'){
                    continue;
                }

                //register widget if widget controller exists
                if (file_exists(BASE_DIR.$widgetDir.$widgetFolder.'/'.$widgetFolder.'.php') && is_file(BASE_DIR.$widgetDir.$widgetFolder.'/'.$widgetFolder.'.php')) {
                    require_once(BASE_DIR.$widgetDir.$widgetFolder.'/'.$widgetFolder.'.php');
                    eval('$widget = new \\Modules\\'.$module['g_name'].'\\'.$module['m_name'].'\\'.Model::WIDGET_DIR.'\\'.$widgetFolder.'($widgetFolder, $module[\'g_name\'], $module[\'m_name\'], $module[\'core\']);');
                    $event->addWidget($widget);
                } else {
                    $widget = new Widget($widgetFolder, $module['g_name'], $module['m_name'], $module['core']);
                    $event->addWidget($widget);
                }

                //scan for js and css files required for widget management
                if ($site->managementState()) {
                    self::includeResources($widgetDir.$widgetFolder, $themeDir.$widgetFolder);

                    $widgetJsFile = $widgetDir.$widgetFolder.'/'.$widgetFolder.'.js';
                    if (file_exists($widgetJsFile) && is_file($widgetJsFile)) {
                        $site->addJavascript( BASE_URL.$widgetJsFile);
                    }

                }
                $publicResourcesDir = $widgetDir.$widgetFolder.'/'.Widget::PUBLIC_DIR;
                $publicResourcesThemeDir = $themeDir.$widgetFolder.'/'.Widget::PUBLIC_DIR;
                self::includeResources($publicResourcesDir, $publicResourcesThemeDir);

            }
        }

    }

    public static function includeResources($resourcesFolder, $overrideFolder){
        global $site;

        if (!file_exists($resourcesFolder) || !is_dir($resourcesFolder) || !file_exists($overrideFolder) || !is_dir($overrideFolder)) {
            return;
        }

        $files = scandir(BASE_DIR.$resourcesFolder);
        if ($files === false) {
            continue;
        }
        foreach ($files as $fileKey => $file) {
            if (is_dir(BASE_DIR.$resourcesFolder.$file)){
                continue;
            }
            if (substr($file, -3) == '.js'){
                $site->addJavascript(BASE_URL.$resourcesFolder.'/'.$file);
            }
            if (substr($file, -4) == '.css'){
                //overriden css version exists
                if (file_exists($overrideFolder.'/'.$file)){
                    $site->addCss(BASE_URL.$overrideFolder.'/'.$file);
                } else {
                    $site->addCss(BASE_URL.$resourcesFolder.'/'.$file);
                }
            }
        }
    }

    public static function duplicatedRevision (\Ip\Event $event) {
        Model::duplicateRevision($event->getValue('basedOn'), $event->getValue('newRevisionId'));
    }



}


