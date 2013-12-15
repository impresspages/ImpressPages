<?php

/**
 * @package ImpressPages
 *
 *
 */
namespace Ip\Module\Pages;


class Forms {
    public static function zoneSeoForm($languageId, $zoneName, $title = null, $url = null, $keywords = null, $description = null)
    {
        //create form object
        $form = new \Ip\Form();


        $field = new \Ip\Form\Field\Hidden(
            array(
                'name' => 'aa',
                'defaultValue' => 'Pages.saveZoneProperties'
            ));
        $form->addField($field);

        $field = new \Ip\Form\Field\Hidden(
            array(
                'name' => 'zoneName',
                'defaultValue' => $zoneName
            ));
        $form->addField($field);

        $field = new \Ip\Form\Field\Hidden(
            array(
                'name' => 'languageId',
                'defaultValue' => $languageId
            ));
        $form->addField($field);

        $field = new \Ip\Form\Field\Text(
            array(
                'name' => 'title',
                'label' => __('Title', 'ipAdmin', false),
                'defaultValue' => $title
            ));
        $form->addField($field);

        $field = new \Ip\Form\Field\Text(
            array(
                'name' => 'url', //html "name" attribute
                'label' => __('URL', 'ipAdmin', false),
                'defaultValue' => $url
            ));
        $form->addField($field);

        $field = new \Ip\Form\Field\Textarea(
            array(
                'name' => 'keywords', //html "name" attribute
                'label' => __('Keywords', 'ipAdmin', false),
                'defaultValue' => $keywords
            ));
        $form->addField($field);

        $field = new \Ip\Form\Field\Textarea(
            array(
                'name' => 'description', //html "name" attribute
                'label' => __('Description', 'ipAdmin', false),
                'defaultValue' => $description
            ));
        $form->addField($field);


        $field = new \Ip\Form\Field\Submit(
            array(
                'name' => 'submit', //html "name" attribute
                'defaultValue' => '{{Save}}'
            ));
        $form->addField($field);


        //print form
        return $form;

    }

    public static function languageForm($languageId, $visible, $title, $abbreviation, $url, $code, $textDirection)
    {
        //create form object
        $form = new \Ip\Form();


        $field = new \Ip\Form\Field\Hidden(
            array(
                'name' => 'aa',
                'defaultValue' => 'Pages.saveLanguageProperties'
            ));
        $form->addField($field);


        $field = new \Ip\Form\Field\Hidden(
            array(
                'name' => 'languageId',
                'defaultValue' => $languageId
            ));
        $form->addField($field);

        $field = new \Ip\Form\Field\Checkbox(
            array(
                'name' => 'visible',
                'label' => __('Visible', 'ipAdmin'),
                'defaultValue' => $visible
            ));
        $form->addField($field);

        $field = new \Ip\Form\Field\Text(
            array(
                'name' => 'title',
                'label' => __('Title', 'ipAdmin'),
                'defaultValue' => $title
            ));
        $form->addField($field);

        $field = new \Ip\Form\Field\Text(
            array(
                'name' => 'abbreviation',
                'label' => __('Abbreviation', 'ipAdmin'),
                'defaultValue' => $abbreviation
            ));
        $form->addField($field);

        $field = new \Ip\Form\Field\Text(
            array(
                'name' => 'url', //html "name" attribute
                'label' => __('URL', 'ipAdmin'),
                'defaultValue' => $url
            ));
        $field->addValidator('Required');
        $form->addField($field);

        $field = new \Ip\Form\Field\Text(
            array(
                'name' => 'code', //html "name" attribute
                'label' => __('RFC 4646 code', 'ipAdmin'),
                'defaultValue' => $code
            ));
        $form->addField($field);

        $field = new \Ip\Form\Field\Select(
            array(
                'name' => 'direction', //html "name" attribute
                'label' => __('Text direction', 'ipAdmin'),
                'defaultValue' => $textDirection
            ));
        $field->setValues(array(array('ltr', 'Left To Right'), array('rtl', 'Right To Left')));
        $form->addField($field);


        $field = new \Ip\Form\Field\Submit(
            array(
                'name' => 'submit', //html "name" attribute
                'defaultValue' => __('Save', 'ipAdmin')
            ));
        $form->addField($field);


        //print form
        return $form;

    }

}