<?php

/**
 * @package ImpressPages
 *
 */

namespace Ip\Internal\System;


class Helper
{

    public static function recoveryPageForm()
    {
        $form = new \Ip\Form();
        $form->setEnvironment(\Ip\Form::ENVIRONMENT_ADMIN);

        $pages = ipDb()->selectAll('page', 'id, title', array('isDeleted' => 1));

        foreach ($pages as $page) {
            $field = new \Ip\Form\Field\Checkbox(
                array(
                    'name' => 'page[]',
                    'label' => $page['title'],
                    'value' => false,
                    'postValue' => $page['id']
                ));
            $form->addField($field);
        }

        return $form;
    }

    public static function emptyPageForm()
    {
        $form = new \Ip\Form();
        $form->setEnvironment(\Ip\Form::ENVIRONMENT_ADMIN);

        $pages = ipDb()->selectAll('page', 'id, title', array('isDeleted' => 1));

        foreach ($pages as $page) {
            $field = new \Ip\Form\Field\Checkbox(
                array(
                    'name' => 'page[]',
                    'label' => $page['title'],
                    'value' => true,
                    'postValue' => $page['id']
                ));
            $form->addField($field);
        }

        return $form;
    }

}
