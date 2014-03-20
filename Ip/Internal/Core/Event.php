<?php
/**
 * @package ImpressPages
 *
 */

namespace Ip\Internal\Core;


class Event
{
    public static function ipBeforeController()
    {
        if (ipConfig()->isDebugMode()) {
            ipAddJs('Ip/Internal/Core/assets/ipCore/jquery.js', null, 10); // default, global jQuery
            ipAddJs('Ip/Internal/Core/assets/ipCore/console.log.js', null, 10);
            ipAddJs('Ip/Internal/Core/assets/ipCore/functions.js');
            ipAddJs('Ip/Internal/Core/assets/ipCore/jquery.tools.form.js');

            ipAddJs('Ip/Internal/Core/assets/ipCore/form/color.js');
            ipAddJs('Ip/Internal/Core/assets/ipCore/form/file.js');
            ipAddJs('Ip/Internal/Core/assets/ipCore/form/richtext.js');
            ipAddJs('Ip/Internal/Core/assets/ipCore/form.js');

            ipAddJs('Ip/Internal/Core/assets/ipCore/validator.js');
            ipAddJs('Ip/Internal/Core/assets/ipCore/widgets.js');
            ipAddJs('Ip/Internal/Core/assets/ipCore/ipCore.js');

        } else {
            ipAddJs('Ip/Internal/Core/assets/ipCore.min.js', null, 10);
        }


        //Form init

        $validatorTranslations = array(
            'ipAdmin' => static::validatorLocalizationData('ipAdmin'),
            ipContent()->getCurrentLanguage()->getCode() => static::validatorLocalizationData('ipPublic')
        );
        ipAddJsVariable('ipValidatorTranslations', $validatorTranslations);


        if (ipAdminId() || \Ip\Internal\Admin\Model::isLoginPage() || \Ip\Internal\Admin\Model::isPasswordResetPage()) {
            if (ipConfig()->isDebugMode()) {
                ipAddJs('Ip/Internal/Core/assets/admin/managementMode.js');
                ipAddJs('Ip/Internal/Core/assets/admin/functions.js');

                ipAddJs('Ip/Internal/Core/assets/admin/form/repositoryFile.js');
                ipAddJs('Ip/Internal/Core/assets/admin/form/color.js');
                ipAddJs('Ip/Internal/Core/assets/admin/form/file.js');
                ipAddJs('Ip/Internal/Core/assets/admin/form/richtext.js');
                ipAddJs('Ip/Internal/Core/assets/admin/form/url.js');
                ipAddJs('Ip/Internal/Core/assets/admin/form.js');

                ipAddJs('Ip/Internal/Core/assets/admin/validator.js');
                ipAddJs('Ip/Internal/Core/assets/admin/bootstrap.js');
            } else {
                ipAddJs('Ip/Internal/Core/assets/admin.min.js', null, 10);
            }

            ipAddJs('Ip/Internal/Core/assets/tinymce/pastePreprocess.js');
            ipAddJs('Ip/Internal/Core/assets/tinymce/default.js');

            ipAddCss('Ip/Internal/Core/assets/admin/admin.css');
        }

        if (ipAdminId()) {

            ipAddJs('Ip/Internal/Core/assets/js/tiny_mce/jquery.tinymce.min.js');
            ipAddJs('Ip/Internal/Core/assets/js/tiny_mce/tinymce.min.js');

            ipAddJsVariable(
                'ipBrowseLinkModalTemplate',
                ipView('view/browseLinkModal.php')->render()
            );
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
