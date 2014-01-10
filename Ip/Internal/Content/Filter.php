<?php


namespace Ip\Internal\Content;


class Filter
{
    public static function ipWidgets($widgets)
    {

        $widgets['IpTitle'] = new \Ip\Internal\Content\Widget\IpTitle\Controller('IpTitle', 'Content', 1);
        $widgets['IpText'] = new \Ip\Internal\Content\Widget\IpText\Controller('IpText', 'Content', 1);
        $widgets['IpSeparator'] = new \Ip\Internal\Content\Widget\IpSeparator\Controller('IpSeparator', 'Content', 1);
        //$widgets['IpTextImage'] = new \Ip\Internal\Content\Widget\IpTextImage\Controller('IpTextImage', 'Content', 1);
        $widgets['IpImage'] = new \Ip\Internal\Content\Widget\IpImage\Controller('IpImage', 'Content', 1);
        $widgets['IpImageGallery'] = new \Ip\Internal\Content\Widget\IpImageGallery\Controller('IpImageGallery', 'Content', 1);
        //$widgets['IpLogoGallery'] = new \Ip\Internal\Content\Widget\IpLogoGallery\Controller('IpLogoGallery', 'Content', 1);
        //$widgets['IpFile'] = new \Ip\Internal\Content\Widget\IpFile\Controller('IpFile', 'Content', 1);
        $widgets['IpHtml'] = new \Ip\Internal\Content\Widget\IpHtml\Controller('IpHtml', 'Content', 1);
        //$widgets['IpFaq'] = new \Ip\Internal\Content\Widget\IpFaq\Controller('IpFaq', 'Content', 1);
        $widgets['IpColumns'] = new \Ip\Internal\Content\Widget\IpColumns\Controller('IpColumns', 'Content', 1);
        $widgets['IpForm'] = new \Ip\Internal\Content\Widget\IpForm\Controller('IpForm', 'Content', 1);


        $widgetDirs = static::getPluginWidgetDirs();
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

    /**
     * IpForm widget
     * @param array $value
     */
    public static function ipWidgetIpFormFieldTypes($fieldTypes, $info = null)
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


    private static function getPluginWidgetDirs()
    {
        $answer = array();
        $plugins = \Ip\Internal\Plugins\Model::getActivePlugins();
        foreach ($plugins as $plugin) {
            $answer = array_merge($answer, static::findPluginWidgets($plugin));
        }
        return $answer;
    }

    private static function findPluginWidgets($moduleName)
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


} 