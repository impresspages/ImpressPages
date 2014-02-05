<?php
/**
 * @package		Library
 *
 *
 */
namespace Modules\developer\std_mod;


class ElementSelect extends Element{ //data element in area
    var $defaultValue;
    var $values;
    var $phpCodeForPreview;


    function __construct($variables){
        $this->order = false;

        parent::__construct($variables);


        if(!isset($variables['dbField']) || $variables['dbField'] == ''){
            $backtrace = debug_backtrace();
            if(isset($backtrace[0]['file']) && $backtrace[0]['line'])
            trigger_error('ElementSelect dbField parameter not set. (Error source: '.$backtrace[0]['file'].' line: '.$backtrace[0]['line'].' ) ');
            else
            trigger_error('ElementSelect dbField parameter not set.');
            exit;
        }

        if(!isset($variables['values']) || $variables['values'] == ''){
            $backtrace = debug_backtrace();
            if(isset($backtrace[0]['file']) && $backtrace[0]['line'])
            trigger_error('ElementSelect values parameter not set. (Error source: '.$backtrace[0]['file'].' line: '.$backtrace[0]['line'].' ) ');
            else
            trigger_error('ElementSelect values parameter not set.');
            exit;
        }

        if(!isset($variables['defaultValue']) || $variables['defaultValue'] == ''){
            $this->defaultValue = $variables['values'][0][0];
        }


        foreach ($variables as $name => $value) {
            switch ($name){
                case 'dbField':
                    $this->dbField = $value;
                    break;
                case 'values':
                    $this->values = $value;
                    break;
                case 'phpCodeForPreview':
                    $this->phpCodeForPreview = $value;
                    break;
            }
        }

    }

    function printFieldNew($prefix, $parentId, $area){
        $html = new StdModHtmlOutput();
        $html->inputSelect($prefix, $this->values, $this->defaultValue, $this->disabledOnInsert);
        return $html->html;
    }



    function printFieldUpdate($prefix, $record, $area){
        $value = null;
         
        $value = $record[$this->dbField];


        $html = new StdModHtmlOutput();
        $html->inputSelect($prefix, $this->values, $value, $this->disabledOnUpdate);
        return $html->html;

        return $answer;
    }


    function getParameters($action, $prefix, $area){
        if(isset($_REQUEST[''.$prefix]) && $_REQUEST[''.$prefix] == ''){
            $_REQUEST[''.$prefix] = null;
        }

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


    function checkField($prefix, $action, $area){
        global $parametersMod;

        if($action != 'update' || !$this->disabledOnUpdate && $this->visibleOnUpdate){
            if($action == 'insert' && $this->disabledOnInsert || $action == 'insert' && !$this->visibleOnInsert)
            $_POST[$prefix] = $this->defaultValue;

            if ($this->required && (!isset($_POST[$prefix]) || $_POST[$prefix] == ''))
            return $std_par = $parametersMod->getValue('developer', 'std_mod','admin_translations','error_required');
        }
    }



    function previewValue($record, $area){
        require_once(BASE_DIR.LIBRARY_DIR.'php/text/string.php');

        $answer = $record[$this->dbField];

        if ($this->phpCodeForPreview) {
            eval ($this->phpCodeForPreview);
        } else {
            foreach($this->values as $valueSet) {
                if ($valueSet[0] == $answer) {
                    $answer = $valueSet[1];
                    break;
                }
            }
        }

        $answer = mb_substr($answer, 0, $this->previewLength);
        $answer = htmlspecialchars($answer);
        $answer = \Library\Php\Text\String::mb_wordwrap($answer, 10, "&#x200B;", 1);
        return $answer;

        return mb_substr($answer, 0, $this->previewLength);
    }



    function setPhpCodeForPreview($phpCodeForPreview){
        $this->phpCodeForPreview = $phpCodeForPreview;
    }

    function printSearchField($level, $key, $area){
        if (isset($_REQUEST['search'][$level][$key])) {
            $value = $_REQUEST['search'][$level][$key];
        } else {
            $value = '';
        }

        $html = new StdModHtmlOutput();
        $html->inputSelect('search['.$level.']['.$key.']', $this->values, $value);
        return $html->html;
    }

    function getFilterOption($value, $area){
        if(!$this->secure)
            $dbField =  "`".$this->dbField."`";
        else
            $dbField =  "AES_DECRYPT(".$this->dbField.", '".$this->secureKey."')";

        return " ".$dbField." = '".mysql_real_escape_string($value)."' ";
    }


}

