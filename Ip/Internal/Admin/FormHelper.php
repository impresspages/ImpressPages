<?php


namespace Ip\Internal\Admin;


class FormHelper
{

    public static  function getLoginForm()
    {
        //create form object
        $form = new \Ip\Form();

        //add text field to form object
        $field = new \Ip\Form\Field\Hidden(
            array(
                'name' => 'sa',
                'value' => 'Admin.loginAjax', //html "name" attribute
            ));
        $form->addfield($field);


        //add text field to form object
        $field = new \Ip\Form\Field\Blank(
            array(
                'name' => 'global_error',
            ));
        $form->addfield($field);

        //add text field to form object
        $field = new \Ip\Form\Field\Text(
            array(
                'name' => 'login', //html "name" attribute
                'label' => __('Username', 'ipAdmin')
            ));
        $field->addValidator('Required');
        $form->addField($field);

        //add text field to form object
        $field = new \Ip\Form\Field\Password(
            array(
                'name' => 'password', //html "name" attribute
                'label' => __('Password', 'ipAdmin')
            ));
        $field->addValidator('Required');
        $form->addField($field);


        //add text field to form object
        $field = new \Ip\Form\Field\Submit(
            array(
                'value' => __('Login', 'ipAdmin')
            ));
        $field->addClass('ipsLoginButton');
        $form->addField($field);

        $form->addClass('ipsLoginForm');

        return $form;
    }

    public static function getPasswordResetForm1()
    {
        //create form object
        $form = new \Ip\Form();

        //add text field to form object
        $field = new \Ip\Form\Field\Hidden(
            array(
                'name' => 'sa',
                'value' => 'Admin.passwordResetAjax', //html "name" attribute
            ));
        $form->addfield($field);


        //add text field to form object
        $field = new \Ip\Form\Field\Blank(
            array(
                'name' => 'global_error',
            ));
        $form->addfield($field);

        //add text field to form object
        $field = new \Ip\Form\Field\Text(
            array(
                'name' => 'username', //html "name" attribute
                'label' => __('Username or email', 'ipAdmin')
            ));
        $field->addValidator('Required');
        $form->addField($field);

        //add text field to form object
        $field = new \Ip\Form\Field\Submit(
            array(
                'value' => __('Reset', 'ipAdmin')
            ));
        $field->addClass('ipsLoginButton');
        $form->addField($field);

        $form->addClass('ipsPasswordResetForm');


        return $form;
    }


    public static function getPasswordResetForm2()
    {
        //create form object
        $form = new \Ip\Form();

        //add text field to form object
        $field = new \Ip\Form\Field\Hidden(
            array(
                'name' => 'sa',
                'value' => 'Admin.passwordResetAjax2', //html "name" attribute
            ));
        $form->addfield($field);

        //add text field to form object
        $field = new \Ip\Form\Field\Hidden(
            array(
                'name' => 'secret',
                'value' => ipRequest()->getQuery('secret', ''), //html "name" attribute
            ));
        $form->addfield($field);

        //add text field to form object
        $field = new \Ip\Form\Field\Hidden(
            array(
                'name' => 'userId',
                'value' => ipRequest()->getQuery('id', ''), //html "name" attribute
            ));
        $form->addfield($field);


        //add text field to form object
        $field = new \Ip\Form\Field\Blank(
            array(
                'name' => 'global_error',
            ));
        $form->addfield($field);

        //add text field to form object
        $field = new \Ip\Form\Field\Password(
            array(
                'name' => 'password', //html "name" attribute
                'label' => __('New password', 'ipAdmin')
            ));
        $field->addValidator('Required');
        $form->addField($field);

        //add text field to form object
        $field = new \Ip\Form\Field\Submit(
            array(
                'value' => __('Save', 'ipAdmin')
            ));
        $field->addClass('ipsLoginButton');
        $form->addField($field);

        $form->addClass('ipsPasswordResetForm2');


        return $form;
    }

}
