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



        return $form;
    }
}