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


        $dispatcher->bind('site.pageDeleted', __NAMESPACE__ .'\System::pageDeleted');

        $dispatcher->bind('site.pageMoved', __NAMESPACE__ .'\System::pageMoved');



        //IpForm widget
        $dispatcher->bind('contentManagement.collectFieldTypes', __NAMESPACE__ .'\System::collectFieldTypes');

        
        
        ipAddJavascript(ipConfig()->coreModuleUrl('Assets/assets/js/jquery.js'));
        ipAddJavascript(ipConfig()->coreModuleUrl('Assets/assets/js/jquery-tools/jquery.tools.form.js'));
        ipAddJavascript(ipConfig()->coreModuleUrl('Content/public/widgets.js'));


        // TODOX move to more appropriate place
        $response = \Ip\ServiceLocator::response();
        if (method_exists($response, 'addJavascriptContent')) {
            $data = array(
                'languageCode' => \Ip\ServiceLocator::content()->getCurrentLanguage()->getCode()
            );

            $validatorJs = \Ip\View::create(ipConfig()->coreModuleFile('Config/jquerytools/validator.js'), $data)->render();
            $response->addJavascriptContent('ipValidatorConfig.js', $validatorJs);
        }

        ipAddJavascript(ipConfig()->coreModuleUrl('Content/assets/widget.admin.min.js'));

        $dispatcher->bind('Admin.login', array($this, 'adminLogin'));


    }

    public function adminLogin($data)
    {
        Service::setManagementMode(1);
    }


    public function executeCron($info)
    {
        if ($info['firstTimeThisDay'] || $info['test']) {
            Model::deleteUnusedWidgets();
        }
    }
    
    public function collectWidgets($widgets)
    {

        $widgets['IpTitle'] = new \Ip\Module\Content\Widget\IpTitle\Controller('IpTitle', 'Content', 1);
        $widgets['IpText'] = new \Ip\Module\Content\Widget\IpText\Controller('IpText', 'Content', 1);
        $widgets['IpSeparator'] = new \Ip\Module\Content\Widget\IpSeparator\Controller('IpSeparator', 'Content', 1);
        $widgets['IpTextImage'] = new \Ip\Module\Content\Widget\IpTextImage\Controller('IpTextImage', 'Content', 1);
        $widgets['IpImage'] = new \Ip\Module\Content\Widget\IpImage\Controller('IpImage', 'Content', 1);
        $widgets['IpImageGallery'] = new \Ip\Module\Content\Widget\IpImageGallery\Controller('IpImageGallery', 'Content', 1);
        $widgets['IpLogoGallery'] = new \Ip\Module\Content\Widget\IpLogoGallery\Controller('IpLogoGallery', 'Content', 1);
        $widgets['IpFile'] = new \Ip\Module\Content\Widget\IpFile\Controller('IpFile', 'Content', 1);
        $widgets['IpTable'] = new \Ip\Module\Content\Widget\IpTable\Controller('IpTable', 'Content', 1);
        $widgets['IpHtml'] = new \Ip\Module\Content\Widget\IpHtml\Controller('IpHtml', 'Content', 1);
        $widgets['IpFaq'] = new \Ip\Module\Content\Widget\IpFaq\Controller('IpFaq', 'Content', 1);
        $widgets['IpColumns'] = new \Ip\Module\Content\Widget\IpColumns\Controller('IpColumns', 'Content', 1);
        $widgets['IpForm'] = new \Ip\Module\Content\Widget\IpForm\Controller('IpForm', 'Content', 1);


        $widgetDirs = $this->getPluginWidgetDirs();
        foreach ($widgetDirs as $widgetDirRecord) {
            $widgetKey = $widgetDirRecord['widgetKey'];
            $widgetClass = '\\Plugin\\' . $widgetDirRecord['module'] . '\\' . Model::WIDGET_DIR . '\\' . $widgetKey . '\\Controller';
            if (class_exists($widgetClass)) {
                $widget = new $widgetClass($widgetKey, $widgetDirRecord['module'], 0);
            } else {
                $widget = new Widget($widgetKey, $widgetDirRecord['module'], $widgetDirRecord['core']);
            }
            $widgets[$widgetDirRecord['widgetKey']] = $widget;
        }
        return $widgets;
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
                $log = \Ip\ServiceLocator::log();
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
     * @param array $value
     */
    public static function collectFieldTypes($fieldTypes, $info = NULL)
    {
        global $parametersMod;
        
        $typeText = $parametersMod->getValue('Form.type_text');
        $typeEmail = $parametersMod->getValue('Form.type_email');
        $typeTextarea = $parametersMod->getValue('Form.type_textarea');
        $typeSelect = $parametersMod->getValue('Form.type_select');
        $typeCheckbox = $parametersMod->getValue('Form.type_checkbox');
        $typeRadio = $parametersMod->getValue('Form.type_radio');
        $typeCaptcha = $parametersMod->getValue('Form.type_captcha');
        $typeFile = $parametersMod->getValue('Form.type_file');

        $fieldTypes['IpText']= new FieldType('IpText', '\Ip\Form\Field\Text', $typeText);
        $fieldTypes['IpEmail']= new FieldType('IpEmail', '\Ip\Form\Field\Email', $typeEmail);
        $fieldTypes['IpTextarea']= new FieldType('IpTextarea', '\Ip\Form\Field\Textarea', $typeTextarea);
        $fieldTypes['IpSelect']= new FieldType('IpSelect', '\Ip\Form\Field\Select', $typeSelect, 'ipWidgetIpForm_InitListOptions', 'ipWidgetIpForm_SaveListOptions', \Ip\View::create('view/form_field_options/list.php')->render());
        $fieldTypes['IpCheckbox']= new FieldType('IpCheckbox', '\Ip\Form\Field\Checkbox', $typeCheckbox, 'ipWidgetIpForm_InitWysiwygOptions', 'ipWidgetIpForm_SaveWysiwygOptions', \Ip\View::create('view/form_field_options/wysiwyg.php')->render());
        $fieldTypes['IpRadio']= new FieldType('IpRadio', '\Ip\Form\Field\Radio', $typeRadio, 'ipWidgetIpForm_InitListOptions', 'ipWidgetIpForm_SaveListOptions', \Ip\View::create('view/form_field_options/list.php')->render());
        $fieldTypes['IpCaptcha']= new FieldType('IpCaptcha', '\Ip\Form\Field\Captcha', $typeCaptcha);
        $fieldTypes['IpFile']= new FieldType('IpFile', '\Ip\Form\Field\File', $typeFile);

        return $fieldTypes;
    }

    
    public static function duplicatedRevision($info)
    {
        Model::duplicateRevision($info['basedOn'], $info['newRevisionId']);
    }

    
    public static function removeRevision ($info) {
        Model::removeRevision($info['revisionId']);
    }
    
    public static function publishRevision($info)
    {
        Model::clearCache($info['revisionId']);
    }

    public static function pageDeleted($info)
    {
        Model::removePageRevisions($info['zoneName'], $info['pageId']);
    }
    
    public static function pageMoved($info)
    {
        if ($info['newZoneName'] != $info['oldZoneName']) {
            //move revisions from one zone to another
            Model::updatePageRevisionsZone($info['pageId'], $info['oldZoneName'], $info['newZoneName']);
        }
    }

}


