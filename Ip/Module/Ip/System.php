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

            $validatorTranslations = array(
                'ipAdmin' => $this->validatorLocalizationData('ipAdmin'),
                ipContent()->getCurrentLanguage()->getCode() => $this->validatorLocalizationData('ipPublic')
            );
            ipAddJavascriptVariable('ipValidatorTranslations', $validatorTranslations);
        }

    }


    protected function validatorLocalizationData($namespace)
    {
        // TODO do this localization on client side
        if ($namespace == 'ipPublic')
        {
            $answer = array(
                '*'           => __('Please correct this value', 'ipPublic'),
                ':email'      => __('Please enter a valid email address', 'ipPublic'),
                ':number'     => __('Please enter a valid numeric value', 'ipPublic'),
                ':url'        => __('Please enter a valid URL', 'ipPublic'),
                '[max]'       => __('Please enter a value no larger than $1', 'ipPublic'),
                '[min]'       => __('Please enter a value of at least $1', 'ipPublic'),
                '[required]'  => __('Please complete this mandatory field', 'ipPublic')
            );
        } elseif ($namespace == 'ipAdmin') {
            $answer = array(
                '*'           => __('Please correct this value', 'ipAdmin'),
                ':email'      => __('Please enter a valid email address', 'ipAdmin'),
                ':number'     => __('Please enter a valid numeric value', 'ipAdmin'),
                ':url'        => __('Please enter a valid URL', 'ipAdmin'),
                '[max]'       => __('Please enter a value no larger than $1', 'ipAdmin'),
                '[min]'       => __('Please enter a value of at least $1', 'ipAdmin'),
                '[required]'  => __('Please complete this mandatory field', 'ipAdmin')
            );
        } else {
            throw new \Ip\CoreException('Unknown translation domain: ' . $namespace);
        }
        return $answer;
    }
}