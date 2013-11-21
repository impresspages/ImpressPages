<?php
/**
 * @package ImpressPages
 *
 */

namespace Ip\Module\Form;


class System
{
    public function init()
    {
        ipAddJavascript(ipConfig()->coreModuleUrl('Assets/assets/js/jquery.js'));
        ipAddJavascript(ipConfig()->coreModuleUrl('Form/assets/form.js'));

        ipAddJavascriptVariable('Form_pluploadJsFile', ipConfig()->coreModuleUrl('Assets/assets/js/plupload/plupload.full.js'));
        ipAddJavascriptVariable('Form_fileFieldJs', ipConfig()->coreModuleUrl('Form/assets/file.js'));


        ipAddJavascriptVariable('Form_spectrumJsFile', ipConfig()->coreModuleUrl('Assets/assets/js/spectrum/spectrum.min.js'));
        ipAddJavascriptVariable('Form_spectrumCssFile', ipConfig()->coreModuleUrl('Assets/assets/js/spectrum/spectrum.css'));
        ipAddJavascriptVariable('Form_colorFieldJs', ipConfig()->coreMOduleUrl('Form/assets/color.js'));

    }
}