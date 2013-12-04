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


        $response = \Ip\ServiceLocator::response();
        if (method_exists($response, 'addJavascriptContent')) {

            //Form init
            ipAddJavascript(ipFileUrl('Ip/Module/Assets/assets/form/form.js'));

            ipAddJavascriptVariable('Form_pluploadJsFile', ipFileUrl('Ip/Module/Assets/assets/js/plupload/plupload.full.js'));
            ipAddJavascriptVariable('Form_fileFieldJs', ipFileUrl('Ip/Module/Assets/assets/form/file.js'));


            ipAddJavascriptVariable('Form_spectrumJsFile', ipFileUrl('Ip/Module/Assets/assets/js/spectrum/spectrum.min.js'));
            ipAddJavascriptVariable('Form_spectrumCssFile', ipFileUrl('Ip/Module/Assets/assets/js/spectrum/spectrum.css'));
            ipAddJavascriptVariable('Form_colorFieldJs', ipFileUrl('Ip/Module/Assets/assets/form/color.js'));


            $validatorTranslations = array(
                'ipAdmin' => $this->validatorLocalizationData('ipAdmin'),
                ipContent()->getCurrentLanguage()->getCode() => $this->validatorLocalizationData('ipPublic')
            );
            ipAddJavascriptVariable('ipValidatorTranslations', $validatorTranslations);
            ipAddJavascript(ipFileUrl('Ip/Module/Ip/assets/validator.js'));

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