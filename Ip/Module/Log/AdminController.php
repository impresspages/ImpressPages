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

        $elements = array();

        $element = new \Ip\Lib\StdMod\Element\Text(array(
            'dbField' => 'plugin'
        ));
        $element->title = __('Plugin', 'ipAdmin');
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
            'dbField' => 'message'
        ));
        $element->title = __('Message', 'ipAdmin');
        $element->showOnList = true;
        $element->disabledOnInsert = true;
        $element->disabledOnUpdate = true;
        $element->previewLength = 200;
        $element->searchable = true;
        $elements[] = $element;

        $element = new \Ip\Lib\StdMod\Element\Text(array(
            'dbField' => 'context'
        ));
        $element->title = __('context', 'ipAdmin');
        $element->showOnList = true;
        $element->disabledOnInsert = true;
        $element->disabledOnUpdate = true;
        $element->searchable = true;
        $element->previewLength = 200;
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
        return new \Ip\Response($std->manage());
    }


}