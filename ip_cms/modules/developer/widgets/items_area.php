<?php

namespace Modules\developer\widgets;

if (!defined('BACKEND')) exit;  //this file can't be accessed directly

require_once(BASE_DIR.MODULE_DIR.'developer/std_mod/std_mod.php'); //include standard module to manage data records

class ItemsArea extends \Modules\developer\std_mod\Area{  //extending standard data management module area

    function __construct(){
        global $parametersMod;  //global object to get parameters

        parent::__construct(
        array(
            'dbTable' => 'm_developer_widget_sort',
            'title' => 'Widgets',
            'dbPrimaryKey' => 'sortId',
            'searchable' => false,
            'orderBy' => 'priority',
            'orderDirection' => 'asc',
            'sortable' => true,
            'sortField' => 'priority',
            'sortType' => 'pointers',
            'allowInsert' => false,
            'allowDelete' => false,
            'allowUpdate' => false
        )
        );
         
        $element = new \Modules\developer\std_mod\ElementText(  //text field
        array(
            'title' => 'Widget name',  //Field name
            'showOnList' => true,  //Show field value in list of all records
            'dbField' => 'widgetName',  //Database field name
            'searchable' => true  //Allow to search by this field
        )
        );
        $this->addElement($element);
    }
}