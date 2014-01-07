<?php
/**
 * @package ImpressPages
 *
 */
namespace Ip\Internal\Content;

use Ip\WidgetController;

class System
{


    function init()
    {

        $dispatcher = ipDispatcher();

        $dispatcher->addEventListener('Ip.initFinished', array($this, 'initWidgets'));
        $dispatcher->addEventListener('Ip.pageRevisionDuplicated', __NAMESPACE__ . '\System::duplicatedRevision');
        $dispatcher->addEventListener('Ip.pageRevisionRemoved', __NAMESPACE__ . '\System::removeRevision');
        $dispatcher->addEventListener('Ip.pageRevisionPublished', __NAMESPACE__ . '\System::publishRevision');
        $dispatcher->addEventListener('Ip.cronExecute', array($this, 'executeCron'));
        $dispatcher->addEventListener('Ip.pageDeleted', __NAMESPACE__ . '\System::pageDeleted');
        $dispatcher->addEventListener('site.pageMoved', __NAMESPACE__ . '\System::pageMoved'); //TODOXX THIS EVENT IS NEVER THROWN #150



        $dispatcher->addFilterListener('Ip.widgets', array($this, 'collectWidgets'));
        $dispatcher->addFilterListener(
            'Ip.widgetIpFormFieldTypes',
            __NAMESPACE__ . '\System::collectFieldTypes'
        );

        ipAddJs(ipFileUrl('Ip/Internal/Content/assets/widgets.js'));

        $ipUrlOverrides = ipConfig()->getRaw('URL_OVERRIDES');
        if (!$ipUrlOverrides) {
            $ipUrlOverrides = array();
        }

        ipAddJsVariable('ipUrlOverrides', $ipUrlOverrides);

        $dispatcher->addEventListener('Ip.adminLoginSuccessful', array($this, 'adminLogin'));
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

        $widgets['IpTitle'] = new \Ip\Internal\Content\Widget\IpTitle\Controller('IpTitle', 'Content', 1);
        $widgets['IpText'] = new \Ip\Internal\Content\Widget\IpText\Controller('IpText', 'Content', 1);
        $widgets['IpSeparator'] = new \Ip\Internal\Content\Widget\IpSeparator\Controller('IpSeparator', 'Content', 1);
        $widgets['IpTextImage'] = new \Ip\Internal\Content\Widget\IpTextImage\Controller('IpTextImage', 'Content', 1);
        $widgets['IpImage'] = new \Ip\Internal\Content\Widget\IpImage\Controller('IpImage', 'Content', 1);
        $widgets['IpImageGallery'] = new \Ip\Internal\Content\Widget\IpImageGallery\Controller('IpImageGallery', 'Content', 1);
        $widgets['IpLogoGallery'] = new \Ip\Internal\Content\Widget\IpLogoGallery\Controller('IpLogoGallery', 'Content', 1);
        $widgets['IpFile'] = new \Ip\Internal\Content\Widget\IpFile\Controller('IpFile', 'Content', 1);
        $widgets['IpTable'] = new \Ip\Internal\Content\Widget\IpTable\Controller('IpTable', 'Content', 1);
        $widgets['IpHtml'] = new \Ip\Internal\Content\Widget\IpHtml\Controller('IpHtml', 'Content', 1);
        $widgets['IpFaq'] = new \Ip\Internal\Content\Widget\IpFaq\Controller('IpFaq', 'Content', 1);
        $widgets['IpColumns'] = new \Ip\Internal\Content\Widget\IpColumns\Controller('IpColumns', 'Content', 1);
        $widgets['IpForm'] = new \Ip\Internal\Content\Widget\IpForm\Controller('IpForm', 'Content', 1);


        $widgetDirs = $this->getPluginWidgetDirs();
        foreach ($widgetDirs as $widgetDirRecord) {
            $widgetKey = $widgetDirRecord['widgetKey'];
            $widgetClass = '\\Plugin\\' . $widgetDirRecord['module'] . '\\' . Model::WIDGET_DIR . '\\' . $widgetKey . '\\Controller';
            if (class_exists($widgetClass)) {
                $widget = new $widgetClass($widgetKey, $widgetDirRecord['module'], 0);
            } else {
                $widget = new \Ip\WidgetController($widgetKey, $widgetDirRecord['module'], 0);
            }
            $widgets[$widgetDirRecord['widgetKey']] = $widget;
        }
        return $widgets;
    }

    private function getPluginWidgetDirs()
    {
        $answer = array();
        $plugins = \Ip\Internal\Plugins\Model::getActivePlugins();
        foreach ($plugins as $plugin) {
            $answer = array_merge($answer, self::findPluginWidgets($plugin));
        }
        return $answer;
    }


    function findPluginWidgets($moduleName)
    {
        $widgetDir = ipFile('Plugin/' . $moduleName . '/' . Model::WIDGET_DIR . '/');

        if (!is_dir($widgetDir)) {
            return array();
        }
        $widgetFolders = scandir($widgetDir);
        if ($widgetFolders === false) {
            return array();
        }

        $answer = array();
        //foreach all widget folders
        foreach ($widgetFolders as $widgetFolder) {
            //each directory is a widget
            if (!is_dir($widgetDir . $widgetFolder) || $widgetFolder == '.' || $widgetFolder == '..') {
                continue;
            }
            if (isset ($answer[(string)$widgetFolder])) {
                ipLog()->warning(
                    'Content.duplicateWidget: {widget}',
                    array('plugin' => 'Content', 'widget' => $widgetFolder)
                );
            }
            $answer[] = array(
                'module' => $moduleName,
                'dir' => $widgetDir . $widgetFolder . '/',
                'widgetKey' => $widgetFolder
            );
        }
        return $answer;
    }

    public function initWidgets()
    {
        //TODO cache found assets to decrease file system usage
        $widgets = Service::getAvailableWidgets();

        if (ipIsManagementState()) {
            foreach ($widgets as $widget) {
                if (!$widget->isCore()) { //core widget assets are included automatically in one minified file
                    $this->addWidgetAssets($widget);
                }
            }
        }

    }

    private function addWidgetAssets(\Ip\WidgetController $widget)
    {
        $pluginAssetsPath = $widget->getModuleName() . '/' . Model::WIDGET_DIR . '/' . $widget->getName(
            ) . '/' . \Ip\Application::ASSETS_DIR . '/';
        if ($widget->isCore()) {
            $widgetPublicDir = 'Ip/Internal/' . $pluginAssetsPath;
        } else {
            $widgetPublicDir = 'Plugin/' . $pluginAssetsPath;
        }


        $this->includeResources($widgetPublicDir);
    }


    private function includeResources($resourcesFolder)
    {

        if (is_dir(ipFile($resourcesFolder))) {
            $files = scandir(ipFile($resourcesFolder));
            if ($files === false) {
                return;
            }


            foreach ($files as $file) {
                if (is_dir(ipFile($resourcesFolder . $file)) && $file != '.' && $file != '..') {
                    self::includeResources(ipFile($resourcesFolder . $file));
                    continue;
                }
                if (strtolower(substr($file, -3)) == '.js') {
                    ipAddJs(ipFileUrl($resourcesFolder . '/' . $file));
                }
                if (strtolower(substr($file, -4)) == '.css') {
                    ipAddCss(ipFileUrl($resourcesFolder . '/' . $file));
                }
            }
        }
    }

    /**
     * IpForm widget
     * @param array $value
     */
    public static function collectFieldTypes($fieldTypes, $info = null)
    {

        $typeText = __('Text', 'ipAdmin', false);
        $typeEmail = __('Email', 'ipAdmin', false);
        $typeTextarea = __('Textarea', 'ipAdmin', false);
        $typeSelect = __('Select', 'ipAdmin', false);
        $typeCheckbox = __('Checkbox', 'ipAdmin', false);
        $typeRadio = __('Radio', 'ipAdmin', false);
        $typeCaptcha = __('Captcha', 'ipAdmin', false);
        $typeFile = __('File', 'ipAdmin', false);

        $fieldTypes['IpText'] = new FieldType('IpText', '\Ip\Form\Field\Text', $typeText);
        $fieldTypes['IpEmail'] = new FieldType('IpEmail', '\Ip\Form\Field\Email', $typeEmail);
        $fieldTypes['IpTextarea'] = new FieldType('IpTextarea', '\Ip\Form\Field\Textarea', $typeTextarea);
        $fieldTypes['IpSelect'] = new FieldType('IpSelect', '\Ip\Form\Field\Select', $typeSelect, 'ipWidgetIpForm_InitListOptions', 'ipWidgetIpForm_SaveListOptions', ipView(
            'view/form_field_options/list.php'
        )->render());
        $fieldTypes['IpCheckbox'] = new FieldType('IpCheckbox', '\Ip\Form\Field\Checkbox', $typeCheckbox, 'ipWidgetIpForm_InitWysiwygOptions', 'ipWidgetIpForm_SaveWysiwygOptions', ipView(
            'view/form_field_options/wysiwyg.php'
        )->render());
        $fieldTypes['IpRadio'] = new FieldType('IpRadio', '\Ip\Form\Field\Radio', $typeRadio, 'ipWidgetIpForm_InitListOptions', 'ipWidgetIpForm_SaveListOptions', ipView(
            'view/form_field_options/list.php'
        )->render());
        $fieldTypes['IpCaptcha'] = new FieldType('IpCaptcha', '\Ip\Form\Field\Captcha', $typeCaptcha);
        $fieldTypes['IpFile'] = new FieldType('IpFile', '\Ip\Form\Field\File', $typeFile);

        return $fieldTypes;
    }


    public static function duplicatedRevision($info)
    {
        Model::duplicateRevision($info['basedOn'], $info['newRevisionId']);
    }


    public static function removeRevision($info)
    {
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


