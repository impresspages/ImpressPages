<?php
/**
 * @package		Library
 *
 *
 */
namespace Modules\developer\std_mod;


class ElementNumber extends Element{ //data element in area
    var $defaultValue;
    var $memValue;
    var $regExpression;
    var $regExpressionError;
    var $maxVal;
    var $minVal;

    function __construct($variables){
        if(!isset($variables['order'])){
            $variables['order'] = true;
        }


        parent::__construct($variables);


        if(!isset($variables['dbField']) || $variables['dbField'] == ''){
            $backtrace = debug_backtrace();
            if(isset($backtrace[0]['file']) && $backtrace[0]['line'])
            trigger_error('ElementNumber dbField parameter not set. (Error source: '.$backtrace[0]['file'].' line: '.$backtrace[0]['line'].' ) ');
            else
            trigger_error('ElementNumber dbField parameter not set.');
            exit;
        }


        foreach ($variables as $name => $value) {
            switch ($name){
                case 'regExpression':
                    $this->regExpression = $value;
                    break;
                case 'regExpressionError':
                    $this->regExpressionError = $value;
                    break;
                case 'dbField':
                    $this->dbField = $value;
                    break;
                case 'minVal':
                    $this->minVal = $value;
                    break;
                case 'maxVal':
                    $this->maxVal = $value;
                    break;
                    break;
            }
        }

    }


    function printFieldNew($prefix, $parentId = null, $area = null){
        $html = new StdModHtmlOutput();
        $value = null;
        if(isset($this->memValue)){
            $html->input($prefix, $this->memValue, $this->disabledOnInsert);
        }else{
            $html->input($prefix, $this->defaultValue, $this->disabledOnInsert);
        }
        return $html->html;

    }



    function printFieldUpdate($prefix, $record, $area = null){
        $value = null;

        $value = $record[$this->dbField];

        $html = new StdModHtmlOutput();
        $html->input($prefix, $value, $this->disabledOnUpdate);
        return $html->html;

    }

    function getParameters($action, $prefix, $area){
        if($action == 'insert'){
            if($this->visibleOnInsert && !$this->disabledOnInsert && $action == 'insert'){
                if($_REQUEST[''.$prefix] == '')
                return array("name"=>$this->dbField, "value"=>null);
                else
                return array("name"=>$this->dbField, "value"=>$_REQUEST[''.$prefix]);
            } else {
                return array("name"=>$this->dbField, "value"=>$this->defaultValue);
            }
        }
        if($action == 'update'){
            if($this->visibleOnUpdate && !$this->disabledOnUpdate && $action == 'update'){
                if($_REQUEST[''.$prefix] == '')
                return array("name"=>$this->dbField, "value"=>null);
                else
                return array("name"=>$this->dbField, "value"=>$_REQUEST[''.$prefix]);
            }
        }


    }



    function previewValue($record, $area){
        $answer = mb_substr($record[$this->dbField], 0, $this->previewLength);
        $answer = htmlspecialchars($answer);
        $answer = wordwrap($answer, 10, "&#x200B;", 1);
        return $answer;
    }

    function checkField($prefix, $action, $area){
        global $parametersMod;


        if($action != 'update' || !$this->disabledOnUpdate && $this->visibleOnUpdate){
            if($action == 'insert' && $this->disabledOnInsert || $action == 'insert' && !$this->visibleOnInsert)
            $_POST[$prefix] = $this->defaultValue;

            if ($this->required && (!isset($_POST[$prefix]) || $_POST[$prefix] == null))
            return $std_par = $parametersMod->getValue('developer', 'std_mod','admin_translations','error_required');

            if($_POST[$prefix] != null && $this->regExpression != null){
                if(!preg_match($this->regExpression, $_POST[$prefix])){
                    return $this->regExpressionError;
                }
            }

            if($_POST[$prefix] != null && !is_numeric($_POST[$prefix]))
            return $parametersMod->getValue('developer', 'std_mod','admin_translations','error_number');

            if($_POST[$prefix] != null && $this->maxVal !== null && $_POST[$prefix] > $this->maxVal)
            return $parametersMod->getValue('developer', 'std_mod','admin_translations','error_number_big').$this->maxVal;
             
            if($_POST[$prefix] != null && $this->minVal !== null &&  $_POST[$prefix] < $this->minVal)
            return $parametersMod->getValue('developer', 'std_mod','admin_translations','error_number_small').$this->minVal;
        }
        return null;
    }




    function printSearchField($level, $key, $area){
        if (isset($_REQUEST['search_'.$key]))
        $value = $_REQUEST['search_'.$key];
        else
        $value = '';
        return '<input name="search['.$level.']['.$key.']" value="'.htmlspecialchars($value).'" />';
    }

    function getFilterOption($value, $area){
        if(!$this->secure)
            $dbField =  "`".$this->dbField."`";
        else
            $dbField =  "AES_DECRYPT(".$this->dbField.", '".$this->secureKey."')";

        return " ".$dbField." like '%".mysql_real_escape_string($value)."%' ";
    }




}

