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
        ipAddJavascript(ipFileUrl('Ip/Module/Assets/assets/form/form.js'));

        ipAddJavascriptVariable('Form_pluploadJsFile', ipFileUrl('Ip/Module/Assets/assets/js/plupload/plupload.full.js'));
        ipAddJavascriptVariable('Form_fileFieldJs', ipFileUrl('Ip/Module/Assets/assets/form/file.js'));


        ipAddJavascriptVariable('Form_spectrumJsFile', ipFileUrl('Ip/Module/Assets/assets/js/spectrum/spectrum.min.js'));
        ipAddJavascriptVariable('Form_spectrumCssFile', ipFileUrl('Ip/Module/Assets/assets/js/spectrum/spectrum.css'));
        ipAddJavascriptVariable('Form_colorFieldJs', ipFileUrl('Ip/Module/Assets/assets/form/color.js'));

        //
    }
}