<?php
/**
 * @package ImpressPages
 *
 */

namespace Ip\Internal\Core;


class Event
{
    public static function ipInit()
    {
        ipAddJs('Ip/Internal/Core/assets/console.log.js', null, 10);
        ipAddJs('Ip/Internal/Core/assets/js/jquery.js', null, 10); // default, global jQuery
        ipAddJs('Ip/Internal/Core/assets/functions.js');
        ipAddJs('Ip/Internal/Core/assets/js/jquery-tools/jquery.tools.form.js');
        ipAddJs('Ip/Internal/Core/assets/form/form.js');
        ipAddJs('Ip/Internal/Core/assets/validator.js');


        //Form init

        $validatorTranslations = array(
            'ipAdmin' => static::validatorLocalizationData('ipAdmin'),
            ipContent()->getCurrentLanguage()->getCode() => static::validatorLocalizationData('ipPublic')
        );
        ipAddJsVariable('ipValidatorTranslations', $validatorTranslations);


        if (ipAdminId() || \Ip\Internal\Admin\Model::isLoginPage()) {
            ipAddJs('Ip/Internal/Core/assets/js/ip.jquery.js', null, 10); // jQuery for core
            ipAddJs('Ip/Internal/Content/assets/managementMode.js');
            ipAddJs('Ip/Internal/Core/assets/adminFunctions.js');

            ipAddJs('Ip/Internal/Core/assets/js/jquery-tools/ip.jquery.tools.form.js');
            ipAddJs('Ip/Internal/Core/assets/form/ip.form.js');
            ipAddJs('Ip/Internal/Core/assets/ip.validator.js');

        }



    }


    protected static function validatorLocalizationData($namespace)
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
            throw new \Ip\Exception('Unknown translation domain: ' . $namespace);
        }
        return $answer;
    }
}
