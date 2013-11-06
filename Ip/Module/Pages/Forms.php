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
        $parametersMod = \Ip\ServiceLocator::getParametersMod();
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
                'label' => $parametersMod->getValue('standard','seo','admin_translations','title'),
                'defaultValue' => $title
            ));
        $form->addField($field);

        $field = new \Ip\Form\Field\Text(
            array(
                'name' => 'url', //html "name" attribute
                'label' => $parametersMod->getValue('standard','seo','admin_translations','url'),
                'defaultValue' => $url
            ));
        $form->addField($field);

        $field = new \Ip\Form\Field\Textarea(
            array(
                'name' => 'keywords', //html "name" attribute
                'label' => $parametersMod->getValue('standard','seo','admin_translations','keywords'),
                'defaultValue' => $keywords
            ));
        $form->addField($field);

        $field = new \Ip\Form\Field\Textarea(
            array(
                'name' => 'description', //html "name" attribute
                'label' => $parametersMod->getValue('standard','seo','admin_translations','description'),
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

}