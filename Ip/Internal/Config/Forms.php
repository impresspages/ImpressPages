<?php
/**
 * @package ImpressPages
 *
 *
 */
namespace Ip\Internal\Config;


class Forms
{
    public static function getForm()
    {
        $form = new \Ip\Form();
        $form->addClass('ipsConfigForm');
        $form->setAjaxSubmit(0);


        $field = new FieldOptionTextLang(
            array(
                'optionName' => 'Config.websiteTitle',
                'name' => 'websiteTitle', //html "name" attribute
                'label' => __('Website title', 'Ip-admin'), //field label that will be displayed next to input field
                'hint' => __('Used as a sender name in emails and as default website logo.', 'Ip-admin')
            ));
        $field->addClass('ipsAutoSave');
        $form->addField($field);


        $field = new FieldOptionTextLang(
            array(
                'optionName' => 'Config.websiteEmail',
                'name' => 'websiteEmail', //html "name" attribute
                'value' => ipGetOptionLang('Config.websiteEmail'),
                'label' => __('Website email', 'Ip-admin'), //field label that will be displayed next to input field
                'hint' => __('Email address used as a sender to send emails on behalf of the website.', 'Ip-admin')
            ));
        $field->addValidator('Email');
        $field->addClass('ipsAutoSave');
        $form->addField($field);


        $field = new \Ip\Form\Field\Checkbox(
            array(
                'name' => 'automaticCron',
                //html "name" attribute
                'value' => ipGetOption('Config.automaticCron', 1),
                'label' => __('Execute cron automatically', 'Ip-admin'),
                //field label that will be displayed next to input field
                'hint' => __(
                    'ImpressPages execute cron once every hour on randomly selected visitor page load. I you have setup cron manually, you can disable automatic cron functionality.',
                    'Ip-admin'
                ),
            ));
        $field->addClass('ipsAutoSave');
        $field->addAttribute('data-fieldid', $field->getName());
        $field->addAttribute('id', $field->getName());
        $field->addAttribute('data-fieldname', $field->getName());
        $form->addField($field);

        $field = new \Ip\Form\Field\Text(
            array(
                'name' => 'cronPassword', //html "name" attribute
                'value' => ipGetOption('Config.cronPassword', 1),
                'label' => __('Cron password', 'Ip-admin'), //field label that will be displayed next to input field
                'hint' => __('Protect cron from being abusively executed by the strangers.', 'Ip-admin'),
                'note' => '<span class="ipsUrlLabel">' . __(
                        'Cron URL: ',
                        'Ip-admin'
                    ) . '</span><a target="_blank" class="ipsUrl"></a>'
            ));
        $field->addClass('ipsAutoSave');
        $field->addAttribute('data-fieldid', $field->getName());
        $field->addAttribute('id', $field->getName());
        $field->addAttribute('data-fieldname', $field->getName());
        $form->addField($field);


        return $form;
    }
}
