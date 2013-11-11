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
        $element->title = __('Module', 'ipAdmin');
        $element->showOnList = true;
        $element->disabledOnInsert = true;
        $element->disabledOnUpdate = true;
        $element->searchable = true;
        $elements[] = $element;


        $element = new \Ip\Lib\StdMod\Element\Text(array(
            'dbField' => 'time'
        ));
        $element->title = __('Time', 'ipAdmin');
        $element->showOnList = true;
        $element->disabledOnInsert = true;
        $element->disabledOnUpdate = true;
        $element->searchable = true;
        $elements[] = $element;


        $element = new \Ip\Lib\StdMod\Element\Text(array(
            'dbField' => 'name'
        ));
        $element->title = __('Name', 'ipAdmin');
        $element->showOnList = true;
        $element->disabledOnInsert = true;
        $element->disabledOnUpdate = true;
        $element->searchable = true;
        $elements[] = $element;

        $element = new \Ip\Lib\StdMod\Element\Text(array(
            'dbField' => 'value_str'
        ));
        $element->title = __('Value string', 'ipAdmin');
        $element->showOnList = true;
        $element->disabledOnInsert = true;
        $element->disabledOnUpdate = true;
        $element->searchable = true;
        $element->previewLength = 1000;
        $elements[] = $element;


        $element = new \Ip\Lib\StdMod\Element\Text(array(
            'dbField' => 'value_int'
        ));
        $element->title = __('Value integer', 'ipAdmin');
        $element->showPnList = true;
        $element->disabledOnInsert = true;
        $element->disabledOnUpdate = true;
        $element->searchable = true;
        $elements[] = $element;

        $element = new \Ip\Lib\StdMod\Element\Text(array(
            'dbField' => 'value_float'
        ));
        $element->title = __('Value float', 'ipAdmin');
        $element->showOnList = true;
        $element->disabledOnInsert = true;
        $element->disabledOnUpdate = true;
        $element->searchable = true;
        $elements[] = $element;

        $area0 = new \Ip\Lib\StdMod\Area();
        $area0->dbTable = "log";
        $area0->title = "Log";
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