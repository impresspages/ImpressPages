<?php
/**
 * @package ImpressPages

 *
 */
namespace Ip\Module\Content;

class System{



    function init(){
        global $site;

        $dispatcher = \Ip\ServiceLocator::getDispatcher();
        
        $dispatcher->bind('contentManagement.collectWidgets', __NAMESPACE__ .'\System::collectWidgets');
        $dispatcher->bind('site.afterInit', __NAMESPACE__ .'\System::initWidgets');
        
        $dispatcher->bind('site.duplicatedRevision', __NAMESPACE__ .'\System::duplicatedRevision');
        
        $dispatcher->bind('site.removeRevision', __NAMESPACE__ .'\System::removeRevision');
        
        $dispatcher->bind('site.publishRevision', __NAMESPACE__ .'\System::publishRevision');
        
        $dispatcher->bind(\Ip\Event\PageDeleted::SITE_PAGE_DELETED, __NAMESPACE__ .'\System::pageDeleted');
        
        $dispatcher->bind(\Ip\Event\PageMoved::SITE_PAGE_MOVED, __NAMESPACE__ .'\System::pageMoved');



        //IpForm widget
        $dispatcher->bind('contentManagement.collectFieldTypes', __NAMESPACE__ .'\System::collectFieldTypes');

        
        
        ipAddJavascript(\Ip\Config::coreModuleUrl('Assets/assets/js/jquery.js'));
        ipAddJavascript(\Ip\Config::libraryUrl('js/jquery-tools/jquery.tools.form.js'));
        ipAddJavascript(\Ip\Config::coreModuleUrl('Content/public/widgets.js'));

        // TODOX Mangirdas has some ideas how to rename tinymce files
        // TODOX load only for management mode
        ipAddJavascript(\Ip\Config::coreModuleUrl('Config/public/tinymce/paste_preprocess'));
        ipAddJavascript(\Ip\Config::coreModuleUrl('Config/public/tinymce/min.js'));
        ipAddJavascript(\Ip\Config::coreModuleUrl('Config/public/tinymce/med.js'));
        ipAddJavascript(\Ip\Config::coreModuleUrl('Config/public/tinymce/max.js'));
        ipAddJavascript(\Ip\Config::coreModuleUrl('Config/public/tinymce/table.js'));

        // TODOX move to more appropriate place
        $response = \Ip\ServiceLocator::getResponse();
        if (method_exists($response, 'addJavascriptContent')) {
            $data = array(
                'languageCode' => \Ip\ServiceLocator::getContent()->getCurrentLanguage()->getCode()
            );

            $validatorJs = \Ip\View::create(\Ip\Config::coreModuleFile('Config/jquerytools/validator.js'), $data)->render();
            $response->addJavascriptContent('ipValidatorConfig.js', $validatorJs);
        }

        if ($site->managementState()) {
            ipAddJavascript(\Ip\Config::coreModuleUrl('Content/public/ipContentManagement.js'));
            ipAddJavascript(\Ip\Config::coreModuleUrl('Content/public/jquery.ip.contentManagement.js'));
            ipAddJavascript(\Ip\Config::coreModuleUrl('Content/public/jquery.ip.pageOptions.js'));
            ipAddJavascript(\Ip\Config::coreModuleUrl('Content/public/jquery.ip.widgetbutton.js'));
            ipAddJavascript(\Ip\Config::coreModuleUrl('Content/public/jquery.ip.block.js'));
            ipAddJavascript(\Ip\Config::coreModuleUrl('Content/public/jquery.ip.widget.js'));
            ipAddJavascript(\Ip\Config::coreModuleUrl('Content/public/exampleContent.js'));
            ipAddJavascript(\Ip\Config::coreModuleUrl('Content/public/drag.js'));


            ipAddJavascript(\Ip\Config::libraryUrl('js/jquery-ui/jquery-ui.js'));
            ipAddCss(\Ip\Config::libraryUrl('js/jquery-ui/jquery-ui.css'));

            ipAddJavascript(\Ip\Config::libraryUrl('js/jquery-tools/jquery.tools.ui.scrollable.js'));

            ipAddJavascript(\Ip\Config::libraryUrl('js/tiny_mce/jquery.tinymce.js'));

            ipAddJavascript(\Ip\Config::libraryUrl('js/plupload/plupload.full.js'));
            ipAddJavascript(\Ip\Config::libraryUrl('js/plupload/plupload.browserplus.js'));
            ipAddJavascript(\Ip\Config::libraryUrl('js/plupload/plupload.gears.js'));
            ipAddJavascript(\Ip\Config::libraryUrl('js/plupload/jquery.plupload.queue/jquery.plupload.queue.js'));


            ipAddJavascript(\Ip\Config::coreModuleUrl('Upload/assets/jquery.ip.uploadImage.js'));
            ipAddJavascript(\Ip\Config::coreModuleUrl('Upload/assets/jquery.ip.uploadFile.js'));

            ipAddCss(\Ip\Config::coreModuleUrl('Content/public/widgets.css'));
            ipAddJavascriptVariable('isMobile', \Ip\Browser::isMobile());

        }

    }

    
    public static function collectWidgets(EventWidget $event){
        $widgetDirs = self::_getWidgetDirs();
        foreach($widgetDirs as $widgetDirRecord) {
            
            $widgetDir = $widgetDirRecord['dir'];
            $widgetKey = $widgetDirRecord['widgetKey'];

            
            //register widget if widget controller exists
            $widgetPhpFile = \Ip\Config::baseFile($widgetDirRecord['dir'].$widgetDirRecord['widgetKey'].'.php');
            if (file_exists($widgetPhpFile) && is_file($widgetPhpFile)) {
                require_once($widgetPhpFile);
                if ($widgetDirRecord['core']) {
                    eval('$widget = new \\Ip\\Module\\'.$widgetDirRecord['module'].'\\'.Model::WIDGET_DIR.'\\'.$widgetKey.'($widgetKey, $widgetDirRecord[\'module\'], $widgetDirRecord[\'core\']);');
                } else {
                    eval('$widget = new \\Plugin\\'.$widgetDirRecord['module'].'\\'.Model::WIDGET_DIR.'\\'.$widgetKey.'($widgetKey, $widgetDirRecord[\'module\'], $widgetDirRecord[\'core\']);');
                }
                $event->addWidget($widget);
            } else {
                $widget = new Widget($widgetKey, $widgetDirRecord['module'], $widgetDirRecord['core']);
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
        foreach($widgetDirs as $widgetRecord) {
            
            $widgetDir = $widgetRecord['dir'];
            $widgetKey = $widgetRecord['widgetKey'];

            // TODOX refactor according to new module structure
            // $themeDir = \Ip\Config::getCore('THEME_DIR').THEME.'/modules/'.$widgetRecord['module'].'/'.Model::WIDGET_DIR.'/';
            
            
            //scan for js and css files required for widget management
            if ($site->managementState()) {
                $publicResourcesDir = $widgetDir.Widget::PUBLIC_DIR;
                // TODOX refactor according to new module structure
                // $publicResourcesThemeDir = $themeDir.$widgetKey.'/'.Widget::PUBLIC_DIR;
                self::includeResources($publicResourcesDir); // self::includeResources($publicResourcesDir, $publicResourcesThemeDir);
                // self::includeResources($publicResourcesThemeDir);
            }
        }
    }
    
    private static function _getWidgetDirs() {
        $answer = array();
        $modules = \Ip\Module\Plugins\Model::getModules();
        foreach ($modules as $module) {
            $answer = array_merge($answer, self::findModuleWidgets($module, 1));
        }

        $plugins = \Ip\Module\Plugins\Model::getactivePlugins();
        foreach ($plugins as $plugin) {
            $answer = array_merge($answer, self::findModuleWidgets($plugin, 0));
        }




        return $answer;
    }

    private static function findModuleWidgets($moduleName, $core)
    {
        if ($core) {
            $widgetDir = 'Ip/Module/' . $moduleName . '/' . Model::WIDGET_DIR.'/';
        } else {
            // TODOX Plugin dir
        }
        if (!is_dir(\Ip\Config::baseFile($widgetDir))) {
            return array();
        }
        $widgetFolders = scandir(\Ip\Config::baseFile($widgetDir));
        if ($widgetFolders === false) {
            return array();
        }

        //foreach all widget folders
        foreach ($widgetFolders as $widgetFolder) {
            //each directory is a widget
            if (!is_dir(\Ip\Config::baseFile($widgetDir.$widgetFolder)) || $widgetFolder == '.' || $widgetFolder == '..'){
                continue;
            }
            if (isset ($answer[(string)$widgetFolder])) {
                $log = \Ip\ServiceLocator::getLog();
                $log->log('stadard', 'content_management', 'duplicatedWidget', $widgetFolder);
            }
            $answer[] = array (
                'module' => $moduleName,
                'core' => $core,
                'dir' => $widgetDir.$widgetFolder.'/',
                'widgetKey' => $widgetFolder
            );
        }
        return $answer;
    }

    public static function includeResources($resourcesFolder, $overrideFolder = null){
        global $site;

        if (is_dir(\Ip\Config::baseFile($resourcesFolder))) {
            $files = scandir(\Ip\Config::baseFile($resourcesFolder));
            if ($files === false) {
                return;
            }
            
            
            foreach ($files as $fileKey => $file) {
                if (is_dir(\Ip\Config::baseFile($resourcesFolder.$file)) && $file != '.' && $file != '..'){
                    self::includeResources(\Ip\Config::baseFile($resourcesFolder.$file), \Ip\Config::baseFile($overrideFolder.$file));
                    continue;
                }
                if (strtolower(substr($file, -3)) == '.js'){
                    //overriden js version exists
                    if (file_exists($overrideFolder.'/'.$file)){
                        ipAddJavascript(\Ip\Config::baseUrl($overrideFolder.'/'.$file));
                    } else {
                        ipAddJavascript(\Ip\Config::baseUrl($resourcesFolder.'/'.$file));
                    }
                }
                if (strtolower(substr($file, -4)) == '.css'){
                    //overriden css version exists
                    if (file_exists($overrideFolder.'/'.$file)){
                        ipAddCss(\Ip\Config::baseUrl($overrideFolder.'/'.$file));
                    } else {
                        ipAddCss(\Ip\Config::baseUrl($resourcesFolder.'/'.$file));
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
        global $parametersMod;
        
        $typeText = $parametersMod->getValue('Form.type_text');
        $typeEmail = $parametersMod->getValue('Form.type_email');
        $typeTextarea = $parametersMod->getValue('Form.type_textarea');
        $typeSelect = $parametersMod->getValue('Form.type_select');
        $typeConfirm = $parametersMod->getValue('Form.type_confirm');
        $typeRadio = $parametersMod->getValue('Form.type_radio');
        $typeCaptcha = $parametersMod->getValue('Form.type_captcha');
        $typeFile = $parametersMod->getValue('Form.type_file');

        $newFieldType = new FieldType('IpText', '\Ip\Form\Field\Text', $typeText);
        $event->addField($newFieldType);
        $newFieldType = new FieldType('IpEmail', '\Ip\Form\Field\Email', $typeEmail);
        $event->addField($newFieldType);
        $newFieldType = new FieldType('IpTextarea', '\Ip\Form\Field\Textarea', $typeTextarea);
        $event->addField($newFieldType);
        $newFieldType = new FieldType('IpSelect', '\Ip\Form\Field\Select', $typeSelect, 'ipWidgetIpForm_InitListOptions', 'ipWidgetIpForm_SaveListOptions', \Ip\View::create('view/form_field_options/list.php')->render());
        $event->addField($newFieldType);
        $newFieldType = new FieldType('IpConfirm', '\Ip\Form\Field\Confirm', $typeConfirm, 'ipWidgetIpForm_InitWysiwygOptions', 'ipWidgetIpForm_SaveWysiwygOptions', \Ip\View::create('view/form_field_options/wysiwyg.php')->render());
        $event->addField($newFieldType);
        $newFieldType = new FieldType('IpRadio', '\Ip\Form\Field\Radio', $typeRadio, 'ipWidgetIpForm_InitListOptions', 'ipWidgetIpForm_SaveListOptions', \Ip\View::create('view/form_field_options/list.php')->render());
        $event->addField($newFieldType);
        $newFieldType = new FieldType('IpCaptcha', '\Ip\Form\Field\Captcha', $typeCaptcha);
        $event->addField($newFieldType);
        $newFieldType = new FieldType('IpFile', '\Ip\Form\Field\File', $typeFile);
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


