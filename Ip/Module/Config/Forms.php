<?php
/**
 * @package ImpressPages
 *
 *
 */
namespace Ip\Module\Config;




class Forms {
    public static function getForm()
    {
        $form = new \Ip\Form();
        $form->addClass('ipsConfigForm');


        //TODOX make multilingual create ipGetOptionLang function
        $field = new \Ip\Form\Field\Text(
            array(
                'name' => 'websiteTitle', //html "name" attribute
                'defaultValue' => ipGetOptionLang('Config.websiteTitle', ipContent()->getCurrentLanguage()->getId()),
                'label' => __('Website title', 'Config'), //field label that will be displayed next to input field
                'hint' => __('Used as a sender name in emails and as default website logo.', 'Config')
            ));
        $field->addClass('ipsAutoSave');
        $field->addClass('ips' . $field->getName());
        $field->addAttribute('data-fieldname', $field->getName());
        $field->addAttribute('data-autosavetype', 'input');
        $form->addField($field);


        //TODOX make multilingual create ipGetOptionLang function
        $field = new \Ip\Form\Field\Email(
            array(
                'name' => 'websiteEmail', //html "name" attribute
                'defaultValue' => ipGetOption('Config.websiteEmail'),
                'label' => __('Website email', 'Config'), //field label that will be displayed next to input field
                'hint' => __('Email address used as a sender to send emails on behalf of the website.', 'Config')
            ));
        $field->addClass('ipsAutoSave');
        $field->addClass('ips' . $field->getName());
        $field->addAttribute('data-fieldname', $field->getName());
        $field->addAttribute('data-autosavetype', 'input');
        $form->addField($field);


        $field = new \Ip\Form\Field\Checkbox(
            array(
                'name' => 'automaticCron', //html "name" attribute
                'defaultValue' => ipGetOption('Config.automaticCron', 1),
                'label' => __('Execute cron automatically', 'Config'), //field label that will be displayed next to input field
                'hint' => __('ImpressPages execute cron once every hour on randomly selected visitor page load. I you have setup cron manually, you can disable automatic cron functionality.', 'Config'),
                'text' => __('Cron URL: ', 'ipAdmin') . '<span class="ipsUrl"></span>'
            ));
        $field->addClass('ipsAutoSave');
        $field->addClass('ips' . $field->getName());
        $field->addAttribute('data-fieldname', $field->getName());
        $field->addAttribute('data-autosavetype', 'checkbox');
        $form->addField($field);

        $field = new \Ip\Form\Field\Text(
            array(
                'name' => 'cronPassword', //html "name" attribute
                'defaultValue' => ipGetOption('Config.cronPassword', 1),
                'label' => __('Cron password', 'Config'), //field label that will be displayed next to input field
                'hint' => __('Protect cron from being abusively executed by the strangers.', 'Config')
            ));
        $field->addClass('ipsAutoSave');
        $field->addClass('ips' . $field->getName());
        $field->addAttribute('data-fieldname', $field->getName());
        $field->addAttribute('data-autosavetype', 'input');
        $form->addField($field);

        $field = new \Ip\Form\Field\Number(
            array(
                'name' => 'keepOldRevision', //html "name" attribute
                'defaultValue' => ipGetOption('Config.keepOldRevision', 1),
                'label' => __('Days to keep old content revisions', 'Config'), //field label that will be displayed next to input field
            ));
        $field->addClass('ipsAutoSave');
        $field->addClass('ips' . $field->getName());
        $field->addAttribute('data-fieldname', $field->getName());
        $field->addAttribute('data-autosavetype', 'number');
        $form->addField($field);




        return $form;
    }
}