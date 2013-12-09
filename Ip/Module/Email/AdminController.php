<?php
/**
 * @package ImpressPages
 *
 *
 */

namespace Ip\Module\Email;


class AdminController extends \Ip\Controller{
    function indexAction(){


        $elements = array();

        $element = new \Ip\Lib\StdMod\Element\Text(array(
            'dbField' => 'to'
        ));
        $element->title = __('To', 'ipAdmin');
        $element->showOnList = true;
        $element->searchable = true;
        $element->sortable = true;
        $elements[] = $element;

        $element = new \Ip\Lib\StdMod\Element\Text(array(
            'dbField' => 'from'
        ));
        $element->title = __('From', 'ipAdmin');
        $element->showOnList = true;
        $element->searchable = true;
        $element->sortable = true;
        $elements[] = $element;


        $element = new \Ip\Lib\StdMod\Element\Text(array(
            'dbField' => 'subject'
        ));
        $element->title = __('Subject', 'ipAdmin');
        $element->showOnList = true;
        $element->searchable = true;
        $element->sortable = true;
        $elements[] = $element;


        $element = new \Ip\Lib\StdMod\Element\Bool(array(
            'dbField' => 'immediate'
        ));
        $element->title = __('Immediate', 'ipAdmin');
        $element->showOnList = true;
        $element->read_only = true;
        $element->searchable = true;
        $element->sortable = true;
        $elements[] = $element;


        $element = new \Ip\Lib\StdMod\Element\Text(array(
            'dbField' => 'send'
        ));
        $element->title = __('Sent on', 'ipAdmin');
        $element->showOnList = true;
        $element->searchable = true;
        $element->sortable = true;
        $elements[] = $element;


//TODOX refactor email preview and attachment elements
//
//        $element = new element_email(array(
//            'dbField' => 'id'
//        ));
//        $element->title = __('Email', 'ipAdmin');
//        $element->showOnList = true;
//        $element->read_only = true;
//        $element->searchable = true;
//        $elements[] = $element;
//
//        $element = new element_attachment(array(
//            'dbField' => 'id'
//        ));
//        $element->title = __('Attachments', 'ipAdmin');
//        $element->showOnList = true;
//        $element->read_only = true;
//        $element->searchable = true;
//        $elements[] = $element;


        $area0 = new \Ip\Lib\StdMod\Area();
        $area0->dbTable = "m_administrator_email_queue";
        $area0->title = __('Email queue', 'ipAdmin');
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
