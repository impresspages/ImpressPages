<?php
/**
 * @package ImpressPages
 *
 *
 */
namespace Ip\Internal\Administrators;




class Helper
{
    public static function createForm()
    {
        $form = new \Ip\Form();


        $field = new \Ip\Form\Field\Text(
            array(
                'name' => 'username', // HTML "name" attribute
                'label' => __('User name', 'ipAdmin', false), // Field label that will be displayed next to input field
            ));
        $field->addValidator("Required");
        $form->addField($field);


        $field = new \Ip\Form\Field\Text(
            array(
                'name' => 'email', // HTML "name" attribute
                'label' => __('Email', 'ipAdmin', false), // Field label that will be displayed next to input field
            ));
        $field->addValidator("Required");
        $form->addField($field);

        $field = new \Ip\Form\Field\Password(
            array(
                'name' => 'password', // HTML "name" attribute
                'label' => __('Password', 'ipAdmin', false), // Field label that will be displayed next to input field
            ));
        $field->addValidator("Required");
        $form->addField($field);


        $field = new \Ip\Form\Field\Submit(
            array(
                'value' => __('', 'ipAdmin', false)
            ));
        $form->addField($field);


        return $form;
    }


    public static function updateForm()
    {
        $form = new \Ip\Form();


        $field = new \Ip\Form\Field\Text(
            array(
                'name' => 'username', // HTML "name" attribute
                'label' => __('User name', 'ipAdmin', false), // Field label that will be displayed next to input field
                'value' => '{{activeAdministrator.username}}'
            ));
        $field->addValidator("Required");
        $form->addField($field);


        $field = new \Ip\Form\Field\Text(
            array(
                'name' => 'email', // HTML "name" attribute
                'label' => __('Email', 'ipAdmin', false), // Field label that will be displayed next to input field
                'value' => '{{activeAdministrator.email}}'
            ));
        $field->addValidator("Required");
        $form->addField($field);

        $field = new \Ip\Form\Field\Password(
            array(
                'name' => 'password', // HTML "name" attribute
                'label' => __('New password (optional)', 'ipAdmin', false), // Field label that will be displayed next to input field
                'value' => ''
            ));
        $form->addField($field);


        $field = new \Ip\Form\Field\Submit(
            array(
                'value' => __('', 'ipAdmin', false)
            ));
        $form->addField($field);

        return $form;
    }

}
