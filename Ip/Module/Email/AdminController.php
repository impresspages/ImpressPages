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
        $element->title = $parametersMod->getValue('Email.to');
        $element->showOnList = true;
        $element->searchable = true;
        $element->sortable = true;
        $elements[] = $element;

        $element = new \Ip\Lib\StdMod\Element\Text(array(
            'dbField' => 'from'
        ));
        $element->title = $parametersMod->getValue('Email.from');
        $element->showOnList = true;
        $element->searchable = true;
        $element->sortable = true;
        $elements[] = $element;


        $element = new \Ip\Lib\StdMod\Element\Text(array(
            'dbField' => 'subject'
        ));
        $element->title = $parametersMod->getValue('Email.subject');
        $element->showOnList = true;
        $element->searchable = true;
        $element->sortable = true;
        $elements[] = $element;


        $element = new \Ip\Lib\StdMod\Element\Bool(array(
            'dbField' => 'immediate'
        ));
        $element->title = $parametersMod->getValue('Email.immediate');
        $element->showOnList = true;
        $element->read_only = true;
        $element->searchable = true;
        $element->sortable = true;
        $elements[] = $element;


        $element = new \Ip\Lib\StdMod\Element\Text(array(
            'dbField' => 'send'
        ));
        $element->title = $parametersMod->getValue('Email.send');
        $element->showOnList = true;
        $element->searchable = true;
        $element->sortable = true;
        $elements[] = $element;


//TODOX refactor email preview and attachment elements
//
//        $element = new element_email(array(
//            'dbField' => 'id'
//        ));
//        $element->title = $parametersMod->getValue('Email.email');
//        $element->showOnList = true;
//        $element->read_only = true;
//        $element->searchable = true;
//        $elements[] = $element;
//
//        $element = new element_attachment(array(
//            'dbField' => 'id'
//        ));
//        $element->title = $parametersMod->getValue('Email.attachments');
//        $element->showOnList = true;
//        $element->read_only = true;
//        $element->searchable = true;
//        $elements[] = $element;


        $area0 = new \Ip\Lib\StdMod\Area();
        $area0->dbTable = "m_administrator_email_queue";
        $area0->title = $parametersMod->getValue('Email.email_queue');
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
        return $std->manage();

    }


}
