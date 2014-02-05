<?php
/**
 * @package		Library
 *
 *
 */
namespace Modules\developer\std_mod;


abstract class Element { //data element in area

    function __construct($variables = array()) {
        $this->title = 'Untitled';
        $this->required = false;
		$this->description = '';
		$this->searchable = false;
        $this->showOnList = false;
        $this->defaultValue = null;
        $this->order = false;
        $this->visibleOnInsert = true;
        $this->visibleOnUpdate = true;
        $this->disabledOnInsert = false;
        $this->disabledOnUpdate = false;
        $this->previewLength = 20;
        $this->useInBreadcrumb = false;
        $this->secure = false;
        $this->secureKey = DB_SECURE_FIELDS_KEY;

        foreach ($variables as $name => $value) {


            switch ($name) {
                case 'title':
                    $this->title = $value;
                    break;
                case 'required':
                    $this->required = $value;
                    break;
				case 'description':
                    if ($value == ""){
                        $this->description = $value;
                    } else {
                        $this->description = "<br /><span class='description'>".$value."</span>";
                    }
                    break;
				case 'searchable':
                    $this->searchable = $value;
                    break;
                case 'showOnList':
                    $this->showOnList = $value;
                    break;
                case 'defaultValue':
                    $this->defaultValue = $value;
                    break;
                case 'order':
                    $this->order = $value;
                    break;
                case 'visibleOnInsert':
                    $this->visibleOnInsert = $value;
                    break;
                case 'visibleOnUpdate':
                    $this->visibleOnUpdate = $value;
                    break;
                case 'disabledOnInsert':
                    $this->disabledOnInsert = $value;
                    break;
                case 'disabledOnUpdate':
                    $this->disabledOnUpdate = $value;
                    break;
                case 'previewLength':
                    $this->previewLength = $value;
                    break;
                case 'useInBreadcrumb':
                    $this->useInBreadcrumb = $value;
                    break;
                case 'secure':
                    $this->secure = $value;
                    break;
                case 'secureKey':
                    $this->secureKey = $value;
                    break;
            }
        }
    }

    abstract public function printFieldNew($prefix, $parentId, $area);

    abstract public function printFieldUpdate($prefix, $record, $area);

    abstract public function getParameters($action, $prefix, $area);

    abstract public function checkField($key, $action, $area);

    abstract public function previewValue($record, $area);



    public function processInsert($prefix, $lastInsertId, $area) {

    }

    public function processUpdate($prefix, $rowId, $area) {

    }

    public function processDelete($area, $id) {

    }

    public function printSearchField($level, $key, $area) {

    }

    public function getFilterOption($value, $area) {

    }




}

