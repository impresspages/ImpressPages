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
        ipAddJavascript(ipUrl('Ip/Module/Assets/assets/form/form.js'));

        ipAddJavascriptVariable('Form_pluploadJsFile', ipUrl('Ip/Module/Assets/assets/js/plupload/plupload.full.js'));
        ipAddJavascriptVariable('Form_fileFieldJs', ipUrl('Ip/Module/Assets/assets/form/file.js'));


        ipAddJavascriptVariable('Form_spectrumJsFile', ipUrl('Ip/Module/Assets/assets/js/spectrum/spectrum.min.js'));
        ipAddJavascriptVariable('Form_spectrumCssFile', ipUrl('Ip/Module/Assets/assets/js/spectrum/spectrum.css'));
        ipAddJavascriptVariable('Form_colorFieldJs', ipUrl('Ip/Module/Assets/assets/form/color.js'));

        //
    }
}