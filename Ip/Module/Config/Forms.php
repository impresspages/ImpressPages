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


        $field = new \Ip\Form\Field\Text(
            array(
                'name' => 'websiteTitle', //html "name" attribute
                'defaultValue' => \Ip\Storage::get('Config', 'websiteTitle'),
                'label' => __('Website title', 'Config'), //field label that will be displayed next to input field
                'hint' => __('Used as a sender name in emails and as default website logo.', 'Config')
            ));
        $field->addClass('ipsAutoSave');
        $field->addClass('ips' . $field->getName());
        $field->addAttribute('data-fieldname', $field->getName());
        $field->addAttribute('data-autosavetype', 'input');
        $form->addField($field);


        $field = new \Ip\Form\Field\Confirm(
            array(
                'name' => 'automaticCron', //html "name" attribute
                'defaultValue' => \Ip\Storage::get('Config', 'automaticCron', 1),
                'label' => __('Execute cron automatically', 'Config'), //field label that will be displayed next to input field
            ));
        $field->addClass('ipsAutoSave');
        $field->addClass('ips' . $field->getName());
        $field->addAttribute('data-fieldname', $field->getName());
        $field->addAttribute('data-autosavetype', 'checkbox');
        $form->addField($field);

        $field = new \Ip\Form\Field\Number(
            array(
                'name' => 'keepOldRevision', //html "name" attribute
                'defaultValue' => \Ip\Storage::get('Config', 'keepOldRevision', 1),
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