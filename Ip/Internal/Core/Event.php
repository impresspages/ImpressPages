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
            ipAddJs('Ip/Internal/Core/assets/ipCore/form/repositoryFile.js');
            ipAddJs('Ip/Internal/Core/assets/ipCore/form/url.js');
            ipAddJs('Ip/Internal/Core/assets/ipCore/form.js');

            ipAddJs('Ip/Internal/Core/assets/ipCore/validator.js');
            ipAddJs('Ip/Internal/Core/assets/ipCore/widgets.js');
            ipAddJs('Ip/Internal/Core/assets/ipCore/ipCore.js');

        } else {
            ipAddJs('Ip/Internal/Core/assets/ipCore.min.js', null, 10);
        }


        //Form init

        $validatorTranslations = array(
            'Ip-admin' => static::validatorLocalizationData('Ip-admin'),
            ipContent()->getCurrentLanguage()->getCode() => static::validatorLocalizationData('Ip')
        );
        ipAddJsVariable('ipValidatorTranslations', $validatorTranslations);


        if (ipAdminId() || \Ip\Internal\Admin\Model::isLoginPage() || \Ip\Internal\Admin\Model::isPasswordResetPage()) {
            if (ipConfig()->isDebugMode()) {
                ipAddJs('Ip/Internal/Core/assets/admin/managementMode.js');
                ipAddJs('Ip/Internal/Core/assets/admin/functions.js');


                ipAddJs('Ip/Internal/Core/assets/admin/validator.js');
                ipAddJs('Ip/Internal/Core/assets/admin/bootstrap/bootstrap.js');
                ipAddJs('Ip/Internal/Core/assets/admin/bootstrap-switch/bootstrap-switch.js');
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


            ipAddJs('Ip/Internal/Core/assets/ipCore/plupload/plupload.full.js');
            ipAddJs('Ip/Internal/Core/assets/ipCore/plupload/plupload.browserplus.js');
            ipAddJs('Ip/Internal/Core/assets/ipCore/plupload/plupload.gears.js');
            ipAddJs('Ip/Internal/Core/assets/ipCore/plupload/jquery.plupload.queue/jquery.plupload.queue.js');


        }
    }


    protected static function validatorLocalizationData($namespace)
    {
        // TODO do this localization on client side
        if ($namespace == 'Ip')
        {
            $answer = array(
                '*'           => __('Please correct this value', 'Ip'),
                ':email'      => __('Please enter a valid email address', 'Ip'),
                ':number'     => __('Please enter a valid numeric value', 'Ip'),
                ':url'        => __('Please enter a valid URL', 'Ip'),
                '[max]'       => __('Please enter a value no larger than $1', 'Ip'),
                '[min]'       => __('Please enter a value of at least $1', 'Ip'),
                '[required]'  => __('Please complete this mandatory field', 'Ip')
            );
        } elseif ($namespace == 'Ip-admin') {
            $answer = array(
                '*'           => __('Please correct this value', 'Ip-admin'),
                ':email'      => __('Please enter a valid email address', 'Ip-admin'),
                ':number'     => __('Please enter a valid numeric value', 'Ip-admin'),
                ':url'        => __('Please enter a valid URL', 'Ip-admin'),
                '[max]'       => __('Please enter a value no larger than $1', 'Ip-admin'),
                '[min]'       => __('Please enter a value of at least $1', 'Ip-admin'),
                '[required]'  => __('Please complete this mandatory field', 'Ip-admin')
            );
        } else {
            throw new \Ip\Exception('Unknown translation domain: ' . esc($namespace));
        }
        return $answer;
    }
}
