<?php
/**
 * @package		Library
 *
 *
 */
namespace Modules\developer\std_mod;

class ElementWysiwyg extends Element{ //data element in area
    var $defaultValue;
    var $regExpression;
    var $regExpressionError;
    var $maxLength;

    function __construct($variables){
        parent::__construct($variables);

        if(!isset($variables['dbField']) || $variables['dbField'] == ''){
            $backtrace = debug_backtrace();
            if(isset($backtrace[0]['file']) && $backtrace[0]['line'])
            trigger_error('ElementWysiwyg dbField parameter not set. (Error source: '.$backtrace[0]['file'].' line: '.$backtrace[0]['line'].' ) ');
            else
            trigger_error('ElementWysiwyg dbField parameter not set.');
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
        $html = new StdModHtmlOutput();

        $html->wysiwyg($prefix, $this->defaultValue, $this->disabledOnInsert);

        return $html->html;
         
    }


    function printFieldUpdate($prefix, $record, $area){
        $value = null;

        $value = $record[$this->dbField];

        $html = new StdModHtmlOutput();
        $answer = '';
        $html->wysiwyg($prefix, $value, $this->disabledOnUpdate);
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
        require_once(BASE_DIR.LIBRARY_DIR.'php/text/string.php');

        return htmlspecialchars(\Library\Php\Text\String::mb_wordwrap(mb_substr($record[$this->dbField], 0, $this->previewLength), 10, "&#x200B;", 1));
    }

    function checkField($prefix, $action, $area){

        if($action != 'update' || !$this->disabledOnUpdate && $this->visibleOnUpdate){
            if($action == 'insert' && $this->disabledOnInsert || $action == 'insert' && !$this->visibleOnInsert)
            $_POST[$prefix] = $this->defaultValue;

            if ($this->required && (!isset($_POST[$prefix]) || $_POST[$prefix] == null))
            return $parametersMod->getValue('developer', 'std_mod','admin_translations','error_required');

            if($this->maxLength != null){
                if (sizeof($_POST[$prefix]) > $this->maxLength) {
                    return $parametersMod->getValue('developer', 'std_mod','admin_translations','error_long');
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
        if (isset($_REQUEST['search_'.$key]))
        $value = $_REQUEST['search_'.$key];
        else
        $value = '';
        return '<input name="search_'.$key.'" value="'.htmlspecialchars($value).'" />';
    }

    function getFilterOption($value, $area){
        if(!$this->secure)
            $dbField =  "`".$this->dbField."`";
        else
            $dbField =  "AES_DECRYPT(".$this->dbField.", '".$this->secureKey."')";

        return " ".$dbField." like '%".mysql_real_escape_string($value)."%' ";
    }



}

