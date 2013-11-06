<?php
/**
 * @package		Library
 *
 *
 */
namespace Ip\Lib\StdMod;

class ElementTextarea extends Element{ //data element in area
    var $defaultValue;
    var $memValue;
    var $regExpression;
    var $regExpressionError;
    var $maxLength;

    function __construct($variables){
        parent::__construct($variables);

        if(!isset($variables['dbField']) || $variables['dbField'] == ''){
            $backtrace = debug_backtrace();
            if(isset($backtrace[0]['file']) && $backtrace[0]['line'])
            trigger_error('ElementTextarea dbField parameter not set. (Error source: '.$backtrace[0]['file'].' line: '.$backtrace[0]['line'].' ) ');
            else
            trigger_error('ElementTextarea dbField parameter not set.');
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
                case 'maxLength':
                    $this->maxLength = $value;
                    break;
                case 'dbField':
                    $this->dbField = $value;
                    break;
            }
        }

    }

    function printFieldNew($prefix, $parentId, $area){
        $html = new \Ip\Lib\StdMod\StdModHtmlOutput();
        $value = null;
        $html->textarea($prefix, $this->defaultValue, $this->disabledOnInsert);
        return $html->html;
         
    }



    function printFieldUpdate($prefix, $record, $area){
        $value = null;

        $value = $record[$this->dbField];

        $html = new \Ip\Lib\StdMod\StdModHtmlOutput();
        $answer = '';
        $html->textarea($prefix, $value, $this->disabledOnUpdate);
        return $html->html;



    }

    function getParameters($action, $prefix, $area){

        if($action == 'insert'){
            if($this->visibleOnInsert && !$this->disabledOnInsert && $action == 'insert'){
                return array("name"=>$this->dbField, "value"=>$_REQUEST[''.$prefix]);
            }else{
                return array("name"=>$this->dbField, "value"=>$this->defaultValue);
            }
        }
        if($action == 'update'){
            if($this->visibleOnUpdate && !$this->disabledOnUpdate && $action == 'update'){
                return array("name"=>$this->dbField, "value"=>$_REQUEST[''.$prefix]);
            }

        }
    }


    function previewValue($record, $area){
        require_once \Ip\Config::libraryFile('php/text/string.php');

        $answer = htmlspecialchars(mb_substr($record[$this->dbField], 0, $this->previewLength));
        $answer = \Library\Php\Text\String::mb_wordwrap($answer, 10, "&#x200B;", 1);
        return $answer;
    }

    function checkField($prefix, $action, $area){
        global $parametersMod;


        if($action != 'update' || !$this->disabledOnUpdate && $this->visibleOnUpdate){
            if($action == 'insert' && $this->disabledOnInsert || $action == 'insert' && !$this->visibleOnInsert)
            $_POST[$prefix] = $this->defaultValue;

            if ($this->required && (!isset($_POST[$prefix]) || $_POST[$prefix] == null))
            return $std_par = $parametersMod->getValue('StdMod.error_required');

            if($this->maxLength != null){
                if (sizeof($_POST[$prefix]) > $this->maxLength) {
                    return $parametersMod->getValue('StdMod.error_long');
                }
            }

            if($this->regExpression != null){
                if($_POST[$prefix] == null || preg_match($this->regExpression, $_POST[$prefix]))
                return null;
                else
                return $this->regExpressionError;
            }

        }
        return null;
    }




    function printSearchField($level, $key, $area){
        if (isset($_GET['search'][$level][$key]))
        $value = $_GET['search'][$level][$key];
        else
        $value = '';
        return '<input name="search['.$level.']['.$key.']" value="'.htmlspecialchars($value).'" />';
    }

    function getFilterOption($value, $area){
        return " `".$this->dbField."` like '%".ip_deprecated_mysql_real_escape_string($value)."%' ";
    }


}

