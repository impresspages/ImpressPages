<?php
/**
 * @package ImpressPages
 *
 */
namespace Ip\Internal\InlineManagement;


class Event
{
    public static function ipBeforeController()
    {

        if (ipIsManagementState()) {
            if (ipConfig()->isDebugMode()) {
                ipAddJs('Ip/Internal/InlineManagement/assets/src/inlineManagement.js');
                ipAddJs('Ip/Internal/InlineManagement/assets/src/inlineManagementControls.js');
                ipAddJs('Ip/Internal/InlineManagement/assets/src/inlineManagementImage.js');
                ipAddJs('Ip/Internal/InlineManagement/assets/src/inlineManagementLogo.js');
                ipAddJs('Ip/Internal/InlineManagement/assets/src/inlineManagementText.js');
                ipAddJs('Ip/Internal/InlineManagement/assets/src/jquery.fontselector.js');
            } else {
                ipAddJs('Ip/Internal/InlineManagement/assets/inlineManagement.min.js');
            }

            ipAddJsVariable('ipModuleInlineManagementControls', ipView('view/management/controls.php')->render());

            ipAddJs('Ip/Internal/Content/assets/jquery.ip.uploadImage.js');

            ipAddJs('Ip/Internal/Core/assets/js/bootstrap-colorpicker/js/bootstrap-colorpicker.min.js');
            ipAddCss('Ip/Internal/Core/assets/js/bootstrap-colorpicker/css/bootstrap-colorpicker.css');
        }


    }

    public static function ipUrlChanged($info)
    {
        $httpExpression = '/^((http|https):\/\/)/i';
        if (!preg_match($httpExpression, $info['oldUrl'])) {
            return;
        }
        if (!preg_match($httpExpression, $info['newUrl'])) {
            return;
        }
        Model::updateUrl($info['oldUrl'], $info['newUrl']);
    }
}


