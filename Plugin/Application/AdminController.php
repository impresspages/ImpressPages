<?php

namespace Plugin\Application;


class AdminController
{
    /**
     * Uncomment this method and you will get admin menu entry.
     */
//    public function index()
//    {
//        return 'Your admin page';
//    }

    /**
     * @ipSubmenu formTest
     */
    public function index() {
        $form = new \Ip\Form();
        $field = new \Ip\Form\Field\RichText();
        $field->setLabel('Text');

        $field->setName('text');
        $form->addField($field);

        $field = new \Ip\Form\Field\File();
        $field->setLabel('File');
        $field->setName('textddd');
        $form->addField($field);

        $field = new \Ip\Form\Field\RepositoryFile();
        $field->setName('textddd');
        $field->setLabel('Repository file');
        $form->addField($field);

        $field = new \Ip\Form\Field\Color();
        $field->setName('textxx');
        $field->setLabel('Color');


        $form->addField($field);

        $field = new \Ip\Form\Field\Text();
        $field->setName('textxx2');
        $field->setLabel('Color');



        $form->addField($field);


        return  $form->render() ;
    }
}
