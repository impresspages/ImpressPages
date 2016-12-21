<?php
/**
 * @package ImpressPages
 *
 *
 */

namespace Ip\Internal\Content;


/**
 *
 * Event dispatcher class
 *
 */
class Helper
{

    /**
     * @param $data
     * @return \Ip\Language
     */
    public static function createLanguage($data)
    {
        $language = new \Ip\Language($data['id'], $data['code'], $data['url'], $data['title'], $data['abbreviation'], $data['isVisible'], $data['textDirection']);
        return $language;
    }

    public static function initManagement()
    {
        $widgets = Service::getAvailableWidgets();
        $snippets = [];
        foreach ($widgets as $widget) {
            $snippetHtml = $widget->adminHtmlSnippet();
            if ($snippetHtml != '') {
                $snippets[] = $snippetHtml;
            }
        }
        ipAddJsVariable('ipWidgetSnippets', $snippets);


        ipAddJsVariable('ipContentInit', Model::initManagementData());



        ipAddJs('Ip/Internal/Core/assets/js/jquery-ui/jquery-ui.js');
        ipAddCss('Ip/Internal/Core/assets/js/jquery-ui/jquery-ui.css');

        if (ipConfig()->isDebugMode()) {
            ipAddJs('Ip/Internal/Content/assets/management/ipContentManagementInit.js');
            ipAddJs('Ip/Internal/Content/assets/management/content.js');
            ipAddJs('Ip/Internal/Content/assets/management/jquery.ip.contentManagement.js');
            ipAddJs('Ip/Internal/Content/assets/management/jquery.ip.widgetbutton.js');
            ipAddJs('Ip/Internal/Content/assets/management/jquery.ip.layoutModal.js');
            ipAddJs('Ip/Internal/Content/assets/management/jquery.ip.block.js');
            ipAddJs('Ip/Internal/Content/assets/management/jquery.ip.widget.js');
            ipAddJs('Ip/Internal/Content/assets/management/exampleContent.js');
            ipAddJs('Ip/Internal/Content/assets/management/drag.js');

            ipAddJs('Ip/Internal/Content/Widget/Columns/assets/Columns.js');
            ipAddJs('Ip/Internal/Content/Widget/File/assets/File.js');
            ipAddJs('Ip/Internal/Content/Widget/File/assets/jquery.ipWidgetFile.js');
            ipAddJs('Ip/Internal/Content/Widget/File/assets/jquery.ipWidgetFileContainer.js');
            ipAddJs('Ip/Internal/Content/Widget/Form/assets/Form.js');
            ipAddJs('Ip/Internal/Content/Widget/Form/assets/FormContainer.js');
            ipAddJs('Ip/Internal/Content/Widget/Form/assets/FormField.js');
            ipAddJs('Ip/Internal/Content/Widget/Form/assets/FormOptions.js');
            ipAddJs('Ip/Internal/Content/Widget/Html/assets/Html.js');
            ipAddJs('Ip/Internal/Content/Widget/Video/assets/Video.js');
            ipAddJs('Ip/Internal/Content/Widget/Image/assets/Image.js');
            ipAddJs('Ip/Internal/Content/Widget/Gallery/assets/Gallery.js');
            ipAddJs('Ip/Internal/Content/Widget/Text/assets/Text.js');
            ipAddJs('Ip/Internal/Content/Widget/Heading/assets/Heading.js');
            ipAddJs('Ip/Internal/Content/Widget/Heading/assets/HeadingModal.js');
            ipAddJs('Ip/Internal/Content/Widget/Map/assets/Map.js');

        } else {
            ipAddJs('Ip/Internal/Content/assets/management.min.js');
        }



        ipAddJs('Ip/Internal/Core/assets/js/jquery-tools/jquery.tools.ui.scrollable.js');


        ipAddJs('Ip/Internal/Content/assets/jquery.ip.uploadImage.js');

        ipAddJsVariable('isMobile', \Ip\Internal\Browser::isMobile());


        ipAddJsVariable(
            'ipWidgetLayoutModalTemplate',
            ipView('view/widgetLayoutModal.php')->render()
        );

    }
}
