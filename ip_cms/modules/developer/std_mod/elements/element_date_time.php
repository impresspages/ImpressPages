<?php
/**
 * @package   Library
 *
 *
 */
namespace Modules\developer\std_mod;


class ElementDateTime extends Element{ //data element in area
    var $regExpression;
    var $regExpressionError;
    var $type; // (mysql/unix) 'mysql' data type or 'unix' data type - integer in database

    function __construct($variables){
        global $parametersMod;

        $this->regExpression = '/^([1-3][0-9]{3,3})-(0?[1-9]|1[0-2])-(0?[1-9]|[1-2][0-9]|3[0-1])\s([0-1][0-9]|2[0-4]):([0-5][0-9]):([0-5][0-9])$/';
        $this->regExpressionError = $parametersMod->getValue('developer', 'std_mod', 'admin_translations', 'incorrect_date_format');
        $this->type = 'mysql';

        if(!isset($variables['order'])){
            $variables['order'] = true;
        }


        parent::__construct($variables);



        if(!isset($variables['dbField']) || $variables['dbField'] == ''){
            $backtrace = debug_backtrace();
            if(isset($backtrace[0]['file']) && $backtrace[0]['line'])
            trigger_error('ElementDateTime dbField parameter not set. (Error source: '.$backtrace[0]['file'].' line: '.$backtrace[0]['line'].' ) ');
            else
            trigger_error('ElementDateTime dbField parameter not set.');
            exit;
        }

        foreach ($variables as $name => $value) {
            switch ($name){
                case 'regExpressionError':
                    $this->regExpressionError = $value;
                    break;
                case 'type':
                    $this->type = $value;
                    break;
                case 'dbField':
                    $this->dbField = $value;
                    break;

            }
        }

    }

    function printFieldNew($prefix, $parentId = null, $area = null){
        $html = new StdModHtmlOutput();
        $value = null;

        $html->dateTime($prefix, $this->defaultValue, $this->disabledOnInsert);

        return $html->html;

    }



    function printFieldUpdate($prefix, $record, $area){
        $value = null;

        $html = new StdModHtmlOutput();

        if($this->type == 'unix')
        {
            $value = date("Y-m-d H:i:s", $record[$this->dbField]);
        }
        else
        {
            $value = $record[$this->dbField];
        }


        $html->dateTime($prefix, $value, $this->disabledOnUpdate);
        return $html->html;
    }

    function getParameters($action, $prefix, $area){

        if(isset($_REQUEST[''.$prefix]))
        {
            if($this->type == 'unix')
            {
                $requestValue = strtotime($_REQUEST[''.$prefix]);
            }
            else
            {
                $requestValue = $_REQUEST[''.$prefix];
            }
        }
        else
        {
            $requestValue = null;
        }



        if($action == 'insert'){
            if($this->visibleOnInsert && !$this->disabledOnInsert && $action == 'insert'){
                return array("name"=>$this->dbField, "value"=>$requestValue);
            }else{
                return array("name"=>$this->dbField, "value"=>$this->defaultValue);
            }
        }
        if($action == 'update'){
            if($this->visibleOnUpdate && !$this->disabledOnUpdate && $action == 'update'){
                return array("name"=>$this->dbField, "value"=>$requestValue);
            }
        }
    }


    function previewValue($record, $area){
        if($this->type == 'unix')
        {
            if ($record[$this->dbField] != '') {            
                $answer = htmlspecialchars(date("Y-m-d H:i:s", $record[$this->dbField]));
            } else {
                $answer = '';
            }
            
        }
        else
        {
            $answer = htmlspecialchars($record[$this->dbField]);
        }
        return $answer;

    }

    function checkField($prefix, $action, $area){
        global $parametersMod;


        if($action != 'update' || !$this->disabledOnUpdate && $this->visibleOnUpdate){
            if($action == 'insert' && $this->disabledOnInsert || $action == 'insert' && !$this->visibleOnInsert)
            $_POST[$prefix] = $this->defaultValue;

            if ($this->required && (!isset($_POST[$prefix]) || $_POST[$prefix] == null))
            return $parametersMod->getValue('developer', 'std_mod','admin_translations','error_required');

            if($this->regExpression != null){
                if($_POST[$prefix] == null || preg_match($this->regExpression, $_POST[$prefix]))
                return null;
                else
                return $this->regExpressionError.'&nbsp;';
            }

        }

        return null;
    }




    function printSearchField($level, $key, $area){

        if (isset($_REQUEST['search'][$level][$key]['from']))
        $valueFrom = $_REQUEST['search'][$level][$key]['from'];
        else
        $valueFrom = '';
        if (isset($_REQUEST['search'][$level][$key]['till']))
        $valueTill = $_REQUEST['search'][$level][$key]['till'];
        else
        $valueTill = '';
        $html = new StdModHtmlOutput();
        $html->html('<span class="label">From</span>');
        $html->dateTime('search['.$level.']['.$key.'][from]', $valueFrom);
        $html->html('<br /><span class="label">Till</span>');
        $html->dateTime('search['.$level.']['.$key.'][till]', $valueTill);
        return $html->html;
    }

    function getFilterOption($value, $area){
        if($this->type == 'unix')
        {
            $from = strtotime($value['from']);
            $till = strtotime($value['till']);
        }
        else
        {
            $from = $value['from'];
            $till = $value['till'];
        }

        if(!$this->secure)
            $dbField =  "`".$this->dbField."`";
        else
            $dbField =  "AES_DECRYPT(".$this->dbField.", '".$this->secureKey."')";

        $answer = '';
        if($from != '')
        {
            $answer .= " ".$dbField." >= '".mysql_real_escape_string($from)."' ";
        }
        if(isset($value['till']) && $value['till'] != '')
        {
            if($answer != '')
            {
                $answer .= " AND ";
            }
            $answer .= " ".$dbField." <= '".mysql_real_escape_string($till)."' ";
        }
        return $answer;
    }


}

