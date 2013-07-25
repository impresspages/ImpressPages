<?php
/**
 * @package ImpressPages

 *
 */
namespace Modules\standard\content_management;
if (!defined('CMS')) exit;


require_once(__DIR__.'/model.php');

class System{



    function init(){
        global $site;
        global $dispatcher;
        
        $dispatcher->bind('contentManagement.collectWidgets', __NAMESPACE__ .'\System::collectWidgets');
        $dispatcher->bind('contentManagement.initWidgets', __NAMESPACE__ .'\System::initWidgets');
        
        $dispatcher->bind('site.duplicatedRevision', __NAMESPACE__ .'\System::duplicatedRevision');
        
        $dispatcher->bind('site.removeRevision', __NAMESPACE__ .'\System::removeRevision');
        
        $dispatcher->bind('site.publishRevision', __NAMESPACE__ .'\System::publishRevision');
        
        $dispatcher->bind(\Ip\Event\PageDeleted::SITE_PAGE_DELETED, __NAMESPACE__ .'\System::pageDeleted');
        
        $dispatcher->bind(\Ip\Event\PageMoved::SITE_PAGE_MOVED, __NAMESPACE__ .'\System::pageMoved');



        //IpForm widget
        $dispatcher->bind('contentManagement.collectFieldTypes', __NAMESPACE__ .'\System::collectFieldTypes');

        
        
        $site->addJavascript(BASE_URL.LIBRARY_DIR.'js/jquery/jquery.js');
        $site->addJavascript(BASE_URL.LIBRARY_DIR.'js/jquery-tools/jquery.tools.form.js');
        $site->addJavascript(BASE_URL.MODULE_DIR.'standard/content_management/public/widgets.js');
        
        $site->addJavascript($site->generateUrl(null, null, array('tinymceConfig.js')));
        $site->addJavascript($site->generateUrl(null, null, array('validatorConfig.js')));
        
        if ($site->managementState()) {
            $site->addJavascript(BASE_URL.MODULE_DIR.'standard/content_management/public/ipContentManagement.js');
            $site->addJavascript(BASE_URL.MODULE_DIR.'standard/content_management/public/jquery.ip.contentManagement.js');
            $site->addJavascript(BASE_URL.MODULE_DIR.'standard/content_management/public/jquery.ip.pageOptions.js');
            $site->addJavascript(BASE_URL.MODULE_DIR.'standard/content_management/public/jquery.ip.widgetbutton.js');
            $site->addJavascript(BASE_URL.MODULE_DIR.'standard/content_management/public/jquery.ip.block.js');
            $site->addJavascript(BASE_URL.MODULE_DIR.'standard/content_management/public/jquery.ip.widget.js');


            $site->addJavascript(BASE_URL.LIBRARY_DIR.'js/jquery-ui/jquery-ui.js');
            $site->addCss(BASE_URL.LIBRARY_DIR.'js/jquery-ui/jquery-ui.css');

            $site->addJavascript(BASE_URL.LIBRARY_DIR.'js/jquery-tools/jquery.tools.ui.scrollable.js');

            $site->addJavascript(BASE_URL.LIBRARY_DIR.'js/tiny_mce/jquery.tinymce.js');

            $site->addJavascript(BASE_URL.LIBRARY_DIR.'js/plupload/plupload.full.js');
            $site->addJavascript(BASE_URL.LIBRARY_DIR.'js/plupload/plupload.browserplus.js');
            $site->addJavascript(BASE_URL.LIBRARY_DIR.'js/plupload/plupload.gears.js');
            $site->addJavascript(BASE_URL.LIBRARY_DIR.'js/plupload/jquery.plupload.queue/jquery.plupload.queue.js');


            $site->addJavascript(BASE_URL.MODULE_DIR.'developer/upload/jquery.ip.uploadImage.js?v=1');
            $site->addJavascript(BASE_URL.MODULE_DIR.'developer/upload/jquery.ip.uploadFile.js?v=1');

            $site->addCss(BASE_URL.MODULE_DIR.'standard/content_management/public/widgets.css');

            $event = new EventWidget(null, 'contentManagement.initWidgets', null);
            $dispatcher->notify($event);
        }

    }

    
    public static function collectWidgets(EventWidget $event){
        global $site;
        $widgetDirs = self::_getWidgetDirs();
        foreach($widgetDirs as $widgetDirKey => $widgetDirRecord) {
            
            $module['g_name'] = $widgetDirRecord['moduleGroup'];
            $module['m_name'] = $widgetDirRecord['moduleName'];
            $module['core'] = $widgetDirRecord['core'];
            $widgetDir = $widgetDirRecord['dir'];
            $widgetKey = $widgetDirRecord['widgetKey'];
            
            
            //register widget if widget controller exists
            $widgetPhpFile = BASE_DIR.$widgetDirRecord['dir'].$widgetDirRecord['widgetKey'].'.php';
            if (file_exists($widgetPhpFile) && is_file($widgetPhpFile)) {
                require_once($widgetPhpFile);
                eval('$widget = new \\Modules\\'.$module['g_name'].'\\'.$module['m_name'].'\\'.Model::WIDGET_DIR.'\\'.$widgetKey.'($widgetKey, $module[\'g_name\'], $module[\'m_name\'], $module[\'core\']);');
                $event->addWidget($widget);
            } else {
                $widget = new Widget($widgetKey, $module['g_name'], $module['m_name'], $module['core']);
                $event->addWidget($widget);
            }

        }
    }
    
    public static function initWidgets () {
        global $site;
        
        //widget JS and CSS are included automatically only in administration state
        if (!$site->managementState()) {
            return;
        }
        
        $widgetDirs = self::_getWidgetDirs();
        foreach($widgetDirs as $widgetDirKey => $widgetDirRecord) {
            
            $module['g_name'] = $widgetDirRecord['moduleGroup'];
            $module['m_name'] = $widgetDirRecord['moduleName'];
            $module['core'] = $widgetDirRecord['core'];
            $widgetDir = $widgetDirRecord['dir'];
            $widgetKey = $widgetDirRecord['widgetKey'];
            $themeDir = THEME_DIR.THEME.'/modules/'.$module['g_name'].'/'.$module['m_name'].'/'.Model::WIDGET_DIR.'/';
            
            
            //scan for js and css files required for widget management
            if ($site->managementState()) {
                $publicResourcesDir = $widgetDir.Widget::PUBLIC_DIR;
                $publicResourcesThemeDir = $themeDir.$widgetKey.'/'.Widget::PUBLIC_DIR;
                self::includeResources($publicResourcesDir, $publicResourcesThemeDir);
                self::includeResources($publicResourcesThemeDir);
            }
        }
    }
    
    private static function _getWidgetDirs() {
        global $site;
        
        $answer = array();
        
        require_once(BASE_DIR.FRONTEND_DIR.'db.php');
        require_once(__DIR__.'/widget.php');
        $modules = \Frontend\Db::getModules();
        
        

         
        //loop all installed modules
        foreach ($modules as $moduleKey => $module) {

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
                continue;
            }
            
            //foeach all widget folders
            foreach ($widgetFolders as $widgetFolderKey => $widgetFolder) {
                //each directory is a widget
                if (!is_dir(BASE_DIR.$widgetDir.$widgetFolder) || $widgetFolder == '.' || $widgetFolder == '..'){
                    continue;
                } 
                if (isset ($answer[(string)$widgetFolder])) {
                    global $log;
                    $log->log('stadard', 'content_management', 'duplicatedWidget', $widgetFolder);
                }
                $answer[] = array (
                    'moduleGroup' => $module['g_name'],
                    'moduleName' => $module['m_name'],
                    'core' => $module['core'],
                    'dir' => $widgetDir.$widgetFolder.'/',
                    'widgetKey' => $widgetFolder 
                );
            }
        }
        return $answer;
    } 

    public static function includeResources($resourcesFolder, $overrideFolder = null){
        global $site;

        if (file_exists(BASE_DIR.$resourcesFolder) && is_dir(BASE_DIR.$resourcesFolder)) {
            $files = scandir(BASE_DIR.$resourcesFolder);
            if ($files === false) {
                return;
            }
            
            
            foreach ($files as $fileKey => $file) {
                if (is_dir(BASE_DIR.$resourcesFolder.$file) && $file != '.' && $file != '..'){
                    self::includeResources(BASE_DIR.$resourcesFolder.$file, BASE_DIR.$overrideFolder.$file);
                    continue;
                }
                if (strtolower(substr($file, -3)) == '.js'){
                    //overriden js version exists
                    if (file_exists($overrideFolder.'/'.$file)){
                        $site->addJavascript(BASE_URL.$overrideFolder.'/'.$file);
                    } else {
                        $site->addJavascript(BASE_URL.$resourcesFolder.'/'.$file);
                    }
                }
                if (strtolower(substr($file, -4)) == '.css'){
                    //overriden css version exists
                    if (file_exists($overrideFolder.'/'.$file)){
                        $site->addCss(BASE_URL.$overrideFolder.'/'.$file);
                    } else {
                        $site->addCss(BASE_URL.$resourcesFolder.'/'.$file);
                    }
                }
            }
        }
    }

    /**
     * IpForm widget
     * @param \Modules\standard\content_managemet\EventFormFields $event
     */
    public static function collectFieldTypes(EventFormFields $event){
        global $site;
        global $parametersMod;
        
        $typeText = $parametersMod->getValue('developer','form','admin_translations','type_text');
        $typeEmail = $parametersMod->getValue('developer','form','admin_translations','type_email');
        $typeTextarea = $parametersMod->getValue('developer','form','admin_translations','type_textarea');
        $typeSelect = $parametersMod->getValue('developer','form','admin_translations','type_select');
        $typeConfirm = $parametersMod->getValue('developer','form','admin_translations','type_confirm');
        $typeRadio = $parametersMod->getValue('developer','form','admin_translations','type_radio');
        $typeCaptcha = $parametersMod->getValue('developer','form','admin_translations','type_captcha');
        $typeFile = $parametersMod->getValue('developer','form','admin_translations','type_file');

        $newFieldType = new FieldType('IpText', '\Modules\developer\form\Field\Text', $typeText);
        $event->addField($newFieldType);
        $newFieldType = new FieldType('IpEmail', '\Modules\developer\form\Field\Email', $typeEmail);
        $event->addField($newFieldType);
        $newFieldType = new FieldType('IpTextarea', '\Modules\developer\form\Field\Textarea', $typeTextarea);
        $event->addField($newFieldType);
        $newFieldType = new FieldType('IpSelect', '\Modules\developer\form\Field\Select', $typeSelect, 'ipWidgetIpForm_InitListOptions', 'ipWidgetIpForm_SaveListOptions', \Ip\View::create('view/form_field_options/list.php')->render());
        $event->addField($newFieldType);
        $newFieldType = new FieldType('IpConfirm', '\Modules\developer\form\Field\Confirm', $typeConfirm, 'ipWidgetIpForm_InitWysiwygOptions', 'ipWidgetIpForm_SaveWysiwygOptions', \Ip\View::create('view/form_field_options/wysiwyg.php')->render());
        $event->addField($newFieldType);
        $newFieldType = new FieldType('IpRadio', '\Modules\developer\form\Field\Radio', $typeRadio, 'ipWidgetIpForm_InitListOptions', 'ipWidgetIpForm_SaveListOptions', \Ip\View::create('view/form_field_options/list.php')->render());
        $event->addField($newFieldType);
        $newFieldType = new FieldType('IpCaptcha', '\Modules\developer\form\Field\Captcha', $typeCaptcha);
        $event->addField($newFieldType);
        $newFieldType = new FieldType('IpFile', '\Modules\developer\form\Field\File', $typeFile);
        $event->addField($newFieldType);
    }

    
    public static function duplicatedRevision (\Ip\Event $event) {
        Model::duplicateRevision($event->getValue('basedOn'), $event->getValue('newRevisionId'));
    }

    
    public static function removeRevision (\Ip\Event $event) {
        $revisionId = $event->getValue('revisionId');
        Model::removeRevision($revisionId);
    }
    
    public static function publishRevision (\Ip\Event $event) {
        $revisionId = $event->getValue('revisionId');
        Model::clearCache($revisionId);
    }

    public static function pageDeleted(\Ip\Event\PageDeleted $event) {
        $zoneName = $event->getZoneName();
        $pageId = $event->getPageId();
        
        Model::removePageRevisions($zoneName, $pageId);
    }
    
    public static function pageMoved(\Ip\Event\PageMoved $event) {
        $sourceZoneName = $event->getSourceZoneName();
        $destinationZoneName = $event->getDestinationZoneName();
        $pageId = $event->getPageId();
        
        if ($sourceZoneName != $destinationZoneName) {
            //move revisions from one zone to another
            Model::updatePageRevisionsZone($pageId, $sourceZoneName, $destinationZoneName);
        } else {
            // do nothing
        }

    }    

}


