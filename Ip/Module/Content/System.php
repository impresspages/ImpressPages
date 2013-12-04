<?php
/**
 * @package ImpressPages

 *
 */
namespace Ip\Module\Content;

use Ip\WidgetController;

class System{



    function init(){

        $dispatcher = ipDispatcher();

        $dispatcher->addEventListener('site.afterInit', array($this, 'initWidgets'));
        $dispatcher->addEventListener('site.duplicatedRevision', __NAMESPACE__ .'\System::duplicatedRevision');
        $dispatcher->addEventListener('site.removeRevision', __NAMESPACE__ .'\System::removeRevision');
        $dispatcher->addEventListener('site.publishRevision', __NAMESPACE__ .'\System::publishRevision');
        $dispatcher->addEventListener('Cron.execute', array($this, 'executeCron'));
        $dispatcher->addEventListener('site.pageDeleted', __NAMESPACE__ .'\System::pageDeleted');
        $dispatcher->addEventListener('site.pageMoved', __NAMESPACE__ .'\System::pageMoved');

        $dispatcher->addFilterListener('contentManagement.collectWidgets', array($this, 'collectWidgets'));
        $dispatcher->addFilterListener('contentManagement.collectFieldTypes', __NAMESPACE__ .'\System::collectFieldTypes');

        ipAddJavascript(ipFileUrl('Ip/Module/Content/assets/bootstrap.js'));
        ipAddJavascript(ipFileUrl('Ip/Module/Ip/assets/js/jquery.js'));
        ipAddJavascript(ipFileUrl('Ip/Module/Ip/assets/js/jquery-tools/jquery.tools.form.js'));
        ipAddJavascript(ipFileUrl('Ip/Module/Content/assets/widgets.js'));




        if (ipConfig()->getRaw('DEBUG_MODE')) {
            ipAddJavascript(ipFileUrl('Ip/Module/Content/assets/widget.admin.src/IpColumns.js'));
            ipAddJavascript(ipFileUrl('Ip/Module/Content/assets/widget.admin.src/IpFaq.js'));
            ipAddJavascript(ipFileUrl('Ip/Module/Content/assets/widget.admin.src/IpFile.js'));
            ipAddJavascript(ipFileUrl('Ip/Module/Content/assets/widget.admin.src/IpForm.js'));
            ipAddJavascript(ipFileUrl('Ip/Module/Content/assets/widget.admin.src/IpFormContainer.js'));
            ipAddJavascript(ipFileUrl('Ip/Module/Content/assets/widget.admin.src/IpFormField.js'));
            ipAddJavascript(ipFileUrl('Ip/Module/Content/assets/widget.admin.src/IpFormOptions.js'));
            ipAddJavascript(ipFileUrl('Ip/Module/Content/assets/widget.admin.src/IpHtml.js'));
            ipAddJavascript(ipFileUrl('Ip/Module/Content/assets/widget.admin.src/IpImage.js'));
            ipAddJavascript(ipFileUrl('Ip/Module/Content/assets/widget.admin.src/IpImageGallery.js'));
            ipAddJavascript(ipFileUrl('Ip/Module/Content/assets/widget.admin.src/IpTable.js'));
            ipAddJavascript(ipFileUrl('Ip/Module/Content/assets/widget.admin.src/IpText.js'));
            ipAddJavascript(ipFileUrl('Ip/Module/Content/assets/widget.admin.src/IpTextImage.js'));
            ipAddJavascript(ipFileUrl('Ip/Module/Content/assets/widget.admin.src/IpTitle.js'));
        } else {
            ipAddJavascript(ipFileUrl('Ip/Module/Content/assets/widget.admin.min.js'));
        }


        $dispatcher->addEventListener('Admin.login', array($this, 'adminLogin'));


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
                $widget = new \Ip\Widget($widgetKey, $widgetDirRecord['module'], $widgetDirRecord['core']);
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
            $answer = array_merge($answer, self::findPluginWidgets($plugin));
        }
        return $answer;
    }


    function findPluginWidgets($moduleName)
    {
        $widgetDir = ipFile('Plugin/' . $moduleName . '/' . Model::WIDGET_DIR.'/');

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
                ipLog()->warning('Content.duplicateWidget: {widget}', array('plugin' => 'Content', 'widget' => $widgetFolder));
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

    private function addWidgetAssets(\Ip\WidgetController $widget, $core)
    {
        $pluginAssetsPath = \Ip\Application::ASSET_DIR . '/' . $widget->getModuleName() . '/' . $widget->getName() . '/' . WidgetController::LAYOUT_DIR . '/';
        if ($core) {
            $widgetPublicDir = ipFile('Ip/Module/' . $pluginAssetsPath);
        } else {
            $widgetPublicDir = ipFile('Plugin/' . $pluginAssetsPath);
        }


        $this->includeResources($widgetPublicDir);
    }


    private function includeResources($resourcesFolder){
        if (is_dir(ipFile($resourcesFolder))) {
            $files = scandir(ipFile($resourcesFolder));
            if ($files === false) {
                return;
            }
            
            
            foreach ($files as $fileKey => $file) {
                if (is_dir(ipFile($resourcesFolder.$file)) && $file != '.' && $file != '..'){
                    self::includeResources(ipFile($resourcesFolder.$file));
                    continue;
                }
                if (strtolower(substr($file, -3)) == '.js'){
                    ipAddJavascript(ipFileUrl($resourcesFolder.'/'.$file));
                }
                if (strtolower(substr($file, -4)) == '.css'){
                    ipAddCss(ipFileUrl($resourcesFolder.'/'.$file));
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

        $typeText = __('Text', 'ipAdmin', false);
        $typeEmail = __('Email', 'ipAdmin', false);
        $typeTextarea = __('Textarea', 'ipAdmin', false);
        $typeSelect = __('Select', 'ipAdmin', false);
        $typeCheckbox = __('Checkbox', 'ipAdmin', false);
        $typeRadio = __('Radio', 'ipAdmin', false);
        $typeCaptcha = __('Captcha', 'ipAdmin', false);
        $typeFile = __('File', 'ipAdmin', false);

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


