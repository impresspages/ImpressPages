<?php
/**
 * @package ImpressPages
 *
 *
 */

namespace Ip\Module\Email;


class AdminController{
    function index(){

        $site = \Ip\ServiceLocator::getSite();
        $parametersMod = \Ip\ServiceLocator::getParametersMod();




        $elements = array();

        $element = new \Ip\Lib\StdMod\Element\Text(array(
            'dbField' => 'to'
        ));
        $element->name = $parametersMod->getValue('administrator', 'email_queue', 'admin_translations', 'to');
        $element->showOnList = true;
        $element->searchable = true;
        $element->sortable = true;
        $elements[] = $element;

        $element = new \Ip\Lib\StdMod\Element\Text(array(
            'dbField' => 'from'
        ));
        $element->name = $parametersMod->getValue('administrator', 'email_queue', 'admin_translations', 'from');
        $element->showOnList = true;
        $element->searchable = true;
        $element->sortable = true;
        $elements[] = $element;


        $element = new \Ip\Lib\StdMod\Element\Text(array(
            'dbField' => 'subject'
        ));
        $element->name = $parametersMod->getValue('administrator', 'email_queue', 'admin_translations', 'subject');
        $element->showOnList = true;
        $element->searchable = true;
        $element->sortable = true;
        $elements[] = $element;
//
//        $element = new \Ip\Lib\StdMod\Element\Bool(array(
//            'dbField' => 'immediate'
//        ));
//        $element->name = $parametersMod->getValue('administrator', 'email_queue', 'admin_translations', 'immediate');
//        $element->showOnList = true;
//        $element->read_only = true;
//        $element->searchable = true;
//        $element->sortable = true;
//        $elements[] = $element;


        $element = new \Ip\Lib\StdMod\Element\Text(array(
            'dbField' => 'send'
        ));
        $element->name = $parametersMod->getValue('administrator', 'email_queue', 'admin_translations', 'send');
        $element->showOnList = true;
        $element->searchable = true;
        $element->sortable = true;
        $elements[] = $element;



//
//        $element = new element_email(array(
//            'dbField' => 'id'
//        ));
//        $element->name = $parametersMod->getValue('administrator', 'email_queue', 'admin_translations', 'email');
//        $element->showOnList = true;
//        $element->read_only = true;
//        $element->searchable = true;
//        $elements[] = $element;
//
//        $element = new element_attachment(array(
//            'dbField' => 'id'
//        ));
//        $element->name = $parametersMod->getValue('administrator', 'email_queue', 'admin_translations', 'attachments');
//        $element->showOnList = true;
//        $element->read_only = true;
//        $element->searchable = true;
//        $elements[] = $element;


        $area0 = new \Ip\Lib\StdMod\Area();
        $area0->dbTable = "m_administrator_email_queue";
        $area0->name = $parametersMod->getValue('administrator', 'email_queue', 'admin_translations', 'email_queue');
        $area0->dbPrimaryKey= "id";
        $area0->elements = $elements;
        $area0->searchable = true;
        $area0->allowInsert = false;
        $area0->allowUpdate = false;
        $area0->allowDelete = false;
        $area0->orderBy = 'id';
        $area0->orderDirection = "desc";
        $area0->rowsPerPage = 100;


        $std = new \Ip\Lib\StdMod\StandardModule($area0, 'Email.index');
        $site->setOutput($std->manage());

    }


}
