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
        $response = \Ip\ServiceLocator::response();
        if (method_exists($response, 'addJavascriptContent')) { //if Layout response
            ipAddJavascript(ipFileUrl('Ip/Module/Ip/assets/console.log.js'), 0);
            ipAddJavascript(ipFileUrl('Ip/Module/Ip/assets/js/jquery.js'), 0);

            ipAddJavascript(ipFileUrl('Ip/Module/Ip/assets/functions.js'));
            ipAddJavascript(ipFileUrl('Ip/Module/Ip/assets/js/jquery-tools/jquery.tools.form.js'));



            //Form init
            ipAddJavascript(ipFileUrl('Ip/Module/Ip/assets/form/form.js'));
            ipAddJavascript(ipFileUrl('Ip/Module/Ip/assets/validator.js'));

            ipAddJavascriptVariable('Form_pluploadJsFile', ipFileUrl('Ip/Module/Ip/assets/js/plupload/plupload.full.js'));
            ipAddJavascriptVariable('Form_fileFieldJs', ipFileUrl('Ip/Module/Ip/assets/form/file.js'));


            ipAddJavascriptVariable('Form_spectrumJsFile', ipFileUrl('Ip/Module/Ip/assets/js/spectrum/spectrum.min.js'));
            ipAddJavascriptVariable('Form_spectrumCssFile', ipFileUrl('Ip/Module/Ip/assets/js/spectrum/spectrum.css'));
            ipAddJavascriptVariable('Form_colorFieldJs', ipFileUrl('Ip/Module/Ip/assets/form/color.js'));


            $validatorTranslations = array(
                'ipAdmin' => $this->validatorLocalizationData('ipAdmin'),
                ipContent()->getCurrentLanguage()->getCode() => $this->validatorLocalizationData('ipPublic')
            );
            ipAddJavascriptVariable('ipValidatorTranslations', $validatorTranslations);
        }

    }


    protected function validatorLocalizationData($namespace)
    {
        $answer = array(
            '*'           => __('Please correct this value', $namespace),
            ':email'      => __('Please enter a valid email address', $namespace),
            ':number'     => __('Please enter a valid numeric value', $namespace),
            ':url'        => __('Please enter a valid URL', $namespace),
            '[max]'       => __('Please enter a value no larger than $1', $namespace),
            '[min]'       => __('Please enter a value of at least $1', $namespace),
            '[required]'  => __('Please complete this mandatory field', $namespace)
        );
        return $answer;
    }
}