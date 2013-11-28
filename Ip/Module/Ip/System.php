<?php
/**
 * @package ImpressPages
 *
 */

namespace Ip\Module\Ip;


class System
{
    public function init()
    {
        ipAddJQuery();

        //Form init
        ipAddJavascript(ipConfig()->coreModuleUrl('Assets/assets/form/form.js'));

        ipAddJavascriptVariable('Form_pluploadJsFile', ipConfig()->coreModuleUrl('Assets/assets/js/plupload/plupload.full.js'));
        ipAddJavascriptVariable('Form_fileFieldJs', ipConfig()->coreModuleUrl('Assets/assets/form/file.js'));


        ipAddJavascriptVariable('Form_spectrumJsFile', ipConfig()->coreModuleUrl('Assets/assets/js/spectrum/spectrum.min.js'));
        ipAddJavascriptVariable('Form_spectrumCssFile', ipConfig()->coreModuleUrl('Assets/assets/js/spectrum/spectrum.css'));
        ipAddJavascriptVariable('Form_colorFieldJs', ipConfig()->coreMOduleUrl('Assets/assets/form/color.js'));

        //
    }
}