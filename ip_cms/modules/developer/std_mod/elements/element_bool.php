<?php
/**
 * @package		Library
 *
 *
 */
namespace Modules\developer\std_mod;


class ElementBool extends Element{ //data element in area

    function __construct($variables){

        if(!isset($variables['order'])){
            $variables['order'] = true;
        }


        parent::__construct($variables);

        if(!isset($variables['dbField']) || $variables['dbField'] == ''){
            $backtrace = debug_backtrace();
            if(isset($backtrace[0]['file']) && $backtrace[0]['line'])
            trigger_error('ElementBool dbField parameter not set. (Error source: '.$backtrace[0]['file'].' line: '.$backtrace[0]['line'].' ) ');
            else
            trigger_error('ElementBool dbField parameter not set.');
            exit;
        }

        foreach ($variables as $name => $value) {
            switch ($name){
                case 'dbField':
                    $this->dbField = $value;
                    break;
                case 'defaultValue':
                    if($value)
                    $this->defaultValue = 1;
                    else
                    $this->defaultValue = 0;
                    break;
            }
        }

    }

    function printFieldNew($prefix, $parentId = null, $area = null){
        $html = new StdModHtmlOutput();
        $html->inputCheckbox($prefix, $this->defaultValue, $this->disabledOnInsert);
        return $html->html;
    }





    function printFieldUpdate($prefix, $record, $area = null){
        $value = null;

        $value = $record[''.$this->dbField];
        $html = new StdModHtmlOutput();
        if ($value){
            $html->inputCheckbox($prefix, true, $this->disabledOnUpdate);
        }else
        $html->inputCheckbox($prefix, false, $this->disabledOnUpdate);
        return $html->html;
    }

    function getParameters($action, $prefix, $area){
        if($action == 'insert'){
            if($this->visibleOnInsert && !$this->disabledOnInsert && $action == 'insert'){
                if (isset($_REQUEST[''.$prefix]))
                $value = 1;
                else
                $value = 0;
                return array("name"=>$this->dbField, "value"=>$value);
            } else {
                return array("name"=>$this->dbField, "value"=>$this->defaultValue);
            }

        }


        if($action == 'update'){
            if($this->visibleOnUpdate && !$this->disabledOnUpdate && $action == 'update'){
                if (isset($_REQUEST[''.$prefix]))
                $value = 1;
                else
                $value = 0;
                return array("name"=>$this->dbField, "value"=>$value);
            }
        }

    }


    function printSearchField($level, $key, $area){
        global $parametersMod;
        $checked1 = '';
        $checked2 = '';
        if (isset($_REQUEST['search'][$level][$key])){
            if($_REQUEST['search'][$level][$key] == 1){
                $checked1 = " checked ";
            }else{
                $checked2 = " checked ";
            }
        }

        return '<span class="label"><input class="stdModRadio" type="radio" '.$checked1.' name="search['.$level.']['.$key.']" value="1" />'.$parametersMod->getValue('developer', 'std_mod', 'admin_translations', 'yes').'</span>'.
		'<span class="label"><input  class="stdModRadio" type="radio" '.$checked2.' name="search['.$level.']['.$key.']" value="0" />'.$parametersMod->getValue('developer', 'std_mod', 'admin_translations', 'no').'</span>';
    }

    function getFilterOption($value, $area){
        if(!$this->secure)
            $dbField =  "`".$this->dbField."`";
        else
            $dbField =  "AES_DECRYPT(".$this->dbField.", '".$this->secureKey."')";

        if($value)
        return " ".$dbField." = 1 ";
        else
        return " ".$dbField." = 0 ";
    }



    function previewValue($record, $area){
        if ($record[$this->dbField] == 1)
        return "+";
        else
        return "-";
    }

    function checkField($prefix, $action, $area){
        return null;
    }




}

?>
