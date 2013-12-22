<?php

/**
 * @package ImpressPages
 *
 *
 */
namespace Ip\Internal\Languages;


class Helper
{
    public static function getAddForm()
    {
        $form = new \Ip\Form();

        $field = new \Ip\Form\Field\Hidden(
            array(
                'name' => 'aa',
                'defaultValue' => 'Languages.addLanguage'
            ));
        $form->addField($field);

        $field = new \Ip\Form\Field\Select(
            array(
                'name' => 'languageCode',
                'values' => array(
                    array('en', 'English')
                )
            ));
        $form->addField($field);

        return $form;
    }







}



