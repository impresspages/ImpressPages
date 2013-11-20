<?php
/**
 * @package ImpressPages

 *
 */
namespace Ip\Module\Content;

class System{



    function init(){

        $dispatcher = ipDispatcher();

        $dispatcher->bind('contentManagement.collectWidgets', array($this, 'collectWidgets'));
        $dispatcher->bind('site.afterInit', array($this, 'initWidgets'));

        $dispatcher->bind('site.duplicatedRevision', __NAMESPACE__ .'\System::duplicatedRevision');

        $dispatcher->bind('site.removeRevision', __NAMESPACE__ .'\System::removeRevision');

        $dispatcher->bind('site.publishRevision', __NAMESPACE__ .'\System::publishRevision');

        $dispatcher->bind('Cron.execute', array($this, 'executeCron'));


        $dispatcher->bind(\Ip\Event\PageDeleted::SITE_PAGE_DELETED, __NAMESPACE__ .'\System::pageDeleted');

        $dispatcher->bind(\Ip\Event\PageMoved::SITE_PAGE_MOVED, __NAMESPACE__ .'\System::pageMoved');



        //IpForm widget
        $dispatcher->bind('contentManagement.collectFieldTypes', __NAMESPACE__ .'\System::collectFieldTypes');

        
        
        ipAddJavascript(ipConfig()->coreModuleUrl('Assets/assets/js/jquery.js'));
        ipAddJavascript(ipConfig()->coreModuleUrl('Assets/assets/js/jquery-tools/jquery.tools.form.js'));
        ipAddJavascript(ipConfig()->coreModuleUrl('Content/public/widgets.js'));


        // TODOX move to more appropriate place
        $response = \Ip\ServiceLocator::getResponse();
        if (method_exists($response, 'addJavascriptContent')) {
            $data = array(
                'languageCode' => \Ip\ServiceLocator::getContent()->getCurrentLanguage()->getCode()
            );

            $validatorJs = \Ip\View::create(ipConfig()->coreModuleFile('Config/jquerytools/validator.js'), $data)->render();
            $response->addJavascriptContent('ipValidatorConfig.js', $validatorJs);
        }


    }


    public function executeCron(\Ip\Event $e)
    {
        if ($e->getValue('firstTimeThisDay') || $e->getValue('test')) {
            Model::deleteUnusedWidgets();
        }
    }
    
    public function collectWidgets(EventWidget $event)
    {
        $event->addWidget(new \Ip\Module\Content\Widget\IpTitle\Controller('IpTitle', 'Content', 1));
        $event->addWidget(new \Ip\Module\Content\Widget\IpText\Controller('IpText', 'Content', 1));
        $event->addWidget(new \Ip\Module\Content\Widget\IpSeparator\Controller('IpSeparator', 'Content', 1));
        $event->addWidget(new \Ip\Module\Content\Widget\IpTextImage\Controller('IpTextImage', 'Content', 1));
        $event->addWidget(new \Ip\Module\Content\Widget\IpImage\Controller('IpImage', 'Content', 1));
        $event->addWidget(new \Ip\Module\Content\Widget\IpImageGallery\Controller('IpImageGallery', 'Content', 1));
        $event->addWidget(new \Ip\Module\Content\Widget\IpLogoGallery\Controller('IpLogoGallery', 'Content', 1));
        $event->addWidget(new \Ip\Module\Content\Widget\IpFile\Controller('IpFile', 'Content', 1));
        $event->addWidget(new \Ip\Module\Content\Widget\IpTable\Controller('IpTable', 'Content', 1));
        $event->addWidget(new \Ip\Module\Content\Widget\IpHtml\Controller('IpHtml', 'Content', 1));
        $event->addWidget(new \Ip\Module\Content\Widget\IpFaq\Controller('IpFaq', 'Content', 1));
        $event->addWidget(new \Ip\Module\Content\Widget\IpColumns\Controller('IpColumns', 'Content', 1));
        $event->addWidget(new \Ip\Module\Content\Widget\IpForm\Controller('IpForm', 'Content', 1));


        $widgetDirs = $this->getPluginWidgetDirs();
        foreach ($widgetDirs as $widgetDirRecord) {
            $widgetKey = $widgetDirRecord['widgetKey'];
            $widgetClass = '\\Plugin\\' . $widgetDirRecord['module'] . '\\' . Model::WIDGET_DIR . '\\' . $widgetKey . '\\Controller';
            if (class_exists($widgetClass)) {
                $widget = new $widgetClass($widgetKey, $widgetDirRecord['module'], 0);
                $event->addWidget($widget);
            } else {
                $widget = new Widget($widgetKey, $widgetDirRecord['module'], $widgetDirRecord['core']);
                $event->addWidget($widget);
            }

        }
        $event->addProcessed();
    }

    private function getPluginWidgetDirs()
    {
        $answer = array();
        $plugins = \Ip\Module\Plugins\Model::getActivePlugins();
        foreach ($plugins as $plugin) {
            $answer = array_merge($answer, self::findPluginWidgets($plugin, 0));
        }
        return $answer;
    }


    function findPluginWidgets($moduleName)
    {
        $widgetDir = ipConfig()->pluginFile($moduleName . '/' . Model::WIDGET_DIR.'/');
        if (!is_dir($widgetDir)) {
            return array();
        }
        $widgetFolders = scandir($widgetDir);
        if ($widgetFolders === false) {
            return array();
        }

        //foreach all widget folders
        foreach ($widgetFolders as $widgetFolder) {
            //each directory is a widget
            if (!is_dir($widgetDir.$widgetFolder) || $widgetFolder == '.' || $widgetFolder == '..'){
                continue;
            }
            if (isset ($answer[(string)$widgetFolder])) {
                $log = \Ip\ServiceLocator::getLog();
                $log->log('Content', 'duplicated widget', 'Widget name ' . $widgetFolder);
            }
            $answer[] = array (
                'module' => $moduleName,
                'dir' => $widgetDir . $widgetFolder.'/',
                'widgetKey' => $widgetFolder
            );
        }
        return $answer;
    }

    public function initWidgets () {
        //TODO cache found assets to decrease file system usage
        $widgets = Service::getAvailableWidgets();

        foreach ($widgets as $widget) {
            $this->addWidgetAssets($widget, 1);
        }
        if (ipIsManagementState()) {
            foreach ($widgets as $widget) {
                $this->addWidgetAssets($widget, 0);
            }
        }

    }

    private function addWidgetAssets(\Ip\Module\Content\WidgetController $widget, $core)
    {
        $pluginAssetsPath = \Ip\Application::ASSET_DIR . '/' . $widget->getModuleName() . '/' . $widget->getName() . '/' . WidgetController::PREVIEW_DIR . '/';
        if ($core) {
            $widgetPublicDir = ipConfig()->coreModuleFile($pluginAssetsPath);
        } else {
            $widgetPublicDir = ipConfig()->pluginFile($pluginAssetsPath);
        }


        $this->includeResources($widgetPublicDir);
    }


    private function includeResources($resourcesFolder){
        if (is_dir(ipConfig()->baseFile($resourcesFolder))) {
            $files = scandir(ipConfig()->baseFile($resourcesFolder));
            if ($files === false) {
                return;
            }
            
            
            foreach ($files as $fileKey => $file) {
                if (is_dir(ipConfig()->baseFile($resourcesFolder.$file)) && $file != '.' && $file != '..'){
                    self::includeResources(ipConfig()->baseFile($resourcesFolder.$file));
                    continue;
                }
                if (strtolower(substr($file, -3)) == '.js'){
                    ipAddJavascript(ipConfig()->baseUrl($resourcesFolder.'/'.$file));
                }
                if (strtolower(substr($file, -4)) == '.css'){
                    ipAddCss(ipConfig()->baseUrl($resourcesFolder.'/'.$file));
                }
            }
        }
    }

    /**
     * IpForm widget
     * @param \Ip\Module\Content\EventFormFields $event
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


