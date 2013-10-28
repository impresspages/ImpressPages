<?php
/**
 * @package ImpressPages
 *
 */

namespace Ip\Module\Log;



class AdminController extends \Ip\Controller
{
    public function index()
    {
        $parametersMod = \Ip\ServiceLocator::getParametersMod();
        $site = \Ip\ServiceLocator::getSite();




        $elements = array();

        $element = new \Ip\Lib\StdMod\Element\Text(array(
            'dbField' => 'module'
        ));
        $element->name = $parametersMod->getValue('administrator', 'log', 'admin_translations', 'module');
        $element->showOnList = true;
        $element->disabledOnInsert = true;
        $element->disabledOnUpdate = true;
        $element->searchable = true;
        $elements[] = $element;


        $element = new \Ip\Lib\StdMod\Element\Text(array(
            'dbField' => 'time'
        ));
        $element->name = $parametersMod->getValue('administrator', 'log', 'admin_translations', 'time');
        $element->showOnList = true;
        $element->disabledOnInsert = true;
        $element->disabledOnUpdate = true;
        $element->searchable = true;
        $elements[] = $element;


        $element = new \Ip\Lib\StdMod\Element\Text(array(
            'dbField' => 'name'
        ));
        $element->name = $parametersMod->getValue('administrator', 'log', 'admin_translations', 'name');
        $element->showOnList = true;
        $element->disabledOnInsert = true;
        $element->disabledOnUpdate = true;
        $element->searchable = true;
        $elements[] = $element;

        $element = new \Ip\Lib\StdMod\Element\Text(array(
            'dbField' => 'value_str'
        ));
        $element->name = $parametersMod->getValue('administrator', 'log', 'admin_translations', 'value_str');
        $element->showOnList = true;
        $element->disabledOnInsert = true;
        $element->disabledOnUpdate = true;
        $element->searchable = true;
        $elements[] = $element;


        $element = new \Ip\Lib\StdMod\Element\Text(array(
            'dbField' => 'value_int'
        ));
        $element->name = $parametersMod->getValue('administrator', 'log', 'admin_translations', 'value_int');
        $element->showPnList = true;
        $element->disabledOnInsert = true;
        $element->disabledOnUpdate = true;
        $element->searchable = true;
        $elements[] = $element;

        $element = new \Ip\Lib\StdMod\Element\Text(array(
            'dbField' => 'value_float'
        ));
        $element->name = $parametersMod->getValue('administrator', 'log', 'admin_translations', 'value_float');
        $element->showOnList = true;
        $element->disabledOnInsert = true;
        $element->disabledOnUpdate = true;
        $element->searchable = true;
        $elements[] = $element;

        $area0 = new \Ip\Lib\StdMod\Area();
        $area0->dbTable = "log";
        $area0->name = "Log";
        $area0->dbPrimaryKey = "id";
        $area0->elements = $elements;
        $area0->searchable = true;
        $area0->permission = 'read_only';
        $area0->orderBy = 'id';
        $area0->orderDirection = 'desc';
        $area0->rowsPerPage= 100;
        $area0->allowInsert = false;
        $area0->allowDelete = false;
        $area0->allowUpdate = false;


        $std = new \Ip\Lib\StdMod\StandardModule($area0, 'Log.index');
        $site->setOutput($std->manage());
    }


}