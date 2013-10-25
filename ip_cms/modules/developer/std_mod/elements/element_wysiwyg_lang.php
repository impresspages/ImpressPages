<?php
/**
 * @package		Library
 *
 *
 */
namespace Modules\developer\std_mod;

class ElementWysiwygLang extends Element{ //data element in area
    var $translationField;
    var $translationTable;
    var $recordIdField;
    var $languageIdField;
    var $defaultValues; //array of default values for all languages
    var $regExpression;
    var $maxLength;

    function __construct($variables){
        parent::__construct($variables);

        $this->recordIdField = 'record_id';
        $this->languageIdField = 'language_id';
        $this->translationField = 'translation';
        $this->maxLenght = null;
        $this->previewLength = 60;

        require_once(BASE_DIR.MODULE_DIR.'developer/std_mod/std_mod_db.php');
        $stdModDb = new StdModDb();

        $languages = $stdModDb->languages();
        $this->defaultValues = array();
        foreach($languages as $key => $language){
            $this->defaultValues[$language['id']] = '';
        }


        if(!isset($variables['translationTable']) || $variables['translationTable'] == ''){
            $backtrace = debug_backtrace();
            if(isset($backtrace[0]['file']) && $backtrace[0]['line'])
            trigger_error('ElementTextLang translationTable parameter not set. (Error source: '.$backtrace[0]['file'].' line: '.$backtrace[0]['line'].' ) ');
            else
            trigger_error('ElementTextLang translationTable parameter not set.');
            exit;
        }

        if(isset($variables['sortable']) && $variables['sortable'] == true){
            $backtrace = debug_backtrace();
            if(isset($backtrace[0]['file']) && $backtrace[0]['line'])
            trigger_error('ElementWysiwygLang can\'t be sortable. (Error source: '.$backtrace[0]['file'].' line: '.$backtrace[0]['line'].' ) ');
            else
            trigger_error('ElementWysiwygLang can\'t be sortable.');
            exit;
        }


        foreach ($variables as $name => $value) {
            switch ($name){
                case 'translationField':
                    $this->translationField = $value;
                    break;
                case 'translationTable':
                    $this->translationTable = $value;
                    break;
                case 'recordIdField':
                    $this->recordIdField = $value;
                    break;
                case 'languageIdField':
                    $this->languageIdField = $value;
                    break;
                case 'defaultValues':
                    $this->defaultValues = $value;
                    break;
                case 'defaultValue':
                    foreach($languages as $key => $language){
                        $this->defaultValues[$language['id']] = $value;
                    }
                    break;
                case 'regExpression':
                    $this->regExpression = $value;
                    break;
                case 'regExpressionError':
                    $this->regExpressionError = $value;
                    break;
                case 'maxLength':
                    $this->maxLength = $value;
                    break;
            }
        }

    }

    function printFieldNew($prefix, $id = null, $area = null){
        $html = new StdModHtmlOutput();
        global $stdModDb;

        $languages = $stdModDb->languages();

        $answer = '';
        foreach($languages as $key => $language){
            $html->html('<span class="label">'.htmlspecialchars($language['d_short']).'</span> <br />');
            if(isset($this->defaultValues[$language['id']]))
            $html->wysiwyg($prefix.'_'.$language['id'], $this->defaultValues[$language['id']], $this->disabledOnInsert, $this->maxLength);
            else
            $html->wysiwyg($prefix.'_'.$language['id'], $this->defaultValues[$language['id']], $this->disabledOnInsert, $this->maxLength);
            $html->html("<br />");
        }

        return $html->html;
    }



    function printFieldUpdate($prefix, $record, $area){
        $html = new StdModHtmlOutput();
        global $stdModDb;

        $answer = '';
        $sql2 = "select *, AES_DECRYPT(".mysql_real_escape_string($this->translationField).", '".$this->secureKey."') AS ".$this->translationField." from `".DB_PREF.mysql_real_escape_string($this->translationTable)."` t where t.`".$this->recordIdField."` = '".(int)$record[$area->dbPrimaryKey]."' ";
        $rs2 = mysql_query($sql2);
        $values = array();
        if (!$rs2) {
            trigger_error("Can not get language field data. ".$sql2." ".mysql_error());
        } else {
            while($lock2 = mysql_fetch_assoc($rs2)){
                $values[$lock2[$this->languageIdField]] = $lock2[$this->translationField];
            }

            $languages = $stdModDb->languages();

            foreach($languages as $key => $language){
                $value = '';
                if(isset($values[$language['id']])){
                    $value = $values[$language['id']];
                }
                $html->html('<span class="label">'.htmlspecialchars($language['d_short']).'</span> <br />');
                $html->wysiwyg($prefix.'_'.$language['id'], $value, $this->disabledOnUpdate);
                $html->html("<br />");
            }

        }

        return $html->html;
    }


    function getParameters($action, $prefix, $area){
        return false;
    }


    function previewValue($record, $area){
        require_once(BASE_DIR.LIBRARY_DIR.'php/text/string.php');

        global $stdModDb;

        $answer='';
        $values = array();

        $sql2 = "select *, AES_DECRYPT(".mysql_real_escape_string($this->translationField).", '".$this->secureKey."') AS ".$this->translationField." from `".DB_PREF.mysql_real_escape_string($this->translationTable)."` t where t.`".$this->recordIdField."` = '".(int)$record[$area->dbPrimaryKey]."' ";
        $rs2 = mysql_query($sql2);
        if (!$rs2) {
            trigger_error("Can not get language field data. ".$sql2." ".mysql_error());
        } else {
            while($lock2 = mysql_fetch_assoc($rs2)){
                $values[$lock2[$this->languageIdField]] = $lock2[$this->translationField];
            }
        }

        $languages = $stdModDb->languages();
        foreach($languages as $key => $language){
            $value = '';
            if(isset($values[$language['id']])){
                $value = $values[$language['id']];
            }
            $answer .= '/'.$value;
        }

        $answer = mb_substr($answer, 0, $this->previewLength);
        $answer = htmlspecialchars($answer);
        $answer = \Library\Php\Text\String::mb_wordwrap($answer, 10, "&#x200B;", 1);
        return $answer;

        return htmlspecialchars();
    }

    function checkField($prefix, $action, $area){
        global $stdModDb;
        global $parametersMod;

        $languages = $stdModDb->languages();

        if ($this->required){
            foreach($languages as $key => $language){
                if(!isset($_REQUEST[$prefix.'_'.$language['id']]) || $_REQUEST[$prefix.'_'.$language['id']] == null)
                return $parametersMod->getValue('developer', 'std_mod','admin_translations','error_required');
            }
        }

        if($this->maxLength != null){
            foreach($languages as $key => $language){
                if (sizeof($_REQUEST[$prefix.'_'.$language['id']]) > $this->maxLength) {
                    return $parametersMod->getValue('developer', 'std_mod','admin_translations','error_long');
                }
            }
        }

        if($this->regExpression != null){

            foreach($languages as $key => $language){
                if($_REQUEST[$prefix.'_'.$language['id']] == null || preg_match($this->regExpression, $_REQUEST[$prefix.'_'.$language['id']]))
                return null;
                else
                return $this->regExpressionError.'&nbsp;';
            }

        }
        return null;
    }

    function processInsert($prefix, $lastInsertId, $area){
        global $stdModDb;
        $languages = $stdModDb->languages();

        foreach($languages as $key => $language){
            if($this->visibleOnInsert && !$this->disabledOnInsert){
                $value = $_REQUEST[$prefix.'_'.$language['id']];
            } else {
                if(isset($this->defaultValues[$language['id']]))
                $value = $this->defaultValues[$language['id']];
                else
                $value = '';
            }


            $sql3 = "update `".DB_PREF.mysql_real_escape_string($this->translationTable)."`
      set `".mysql_real_escape_string($this->translationField)."` = AES_ENCRYPT('".mysql_real_escape_string($value)."', '".$this->secureKey."')
      where `".mysql_real_escape_string($this->languageIdField)."`  = '".(int)$language['id']."' and `".mysql_real_escape_string($this->recordIdField)."` = '".$lastInsertId."' ";
            $rs3 = mysql_query($sql3);

            if ($rs3){
                if($stdModDb->updatedRowsCount() == 0){
                    $sql4 = "insert into `".DB_PREF.mysql_real_escape_string($this->translationTable)."`
          set `".mysql_real_escape_string($this->translationField)."` = AES_ENCRYPT('".mysql_real_escape_string($value)."', '".$this->secureKey."'),
          `".mysql_real_escape_string($this->languageIdField)."`  = '".(int)$language['id']."', `".mysql_real_escape_string($this->recordIdField)."` = '".$lastInsertId."' ";
                    $rs4 = mysql_query($sql4);
                    if(!$rs4)
                    trigger_error($sql4." ".mysql_error());

                }
            }else{
                trigger_error("Can't insert language field values ".$sql3." ".mysql_error());
            }


        }
    }
    function processUpdate($prefix, $rowId, $area){
        global $stdModDb;

        if($this->visibleOnUpdate && !$this->disabledOnUpdate){

            $languages = $stdModDb->languages();

            foreach($languages as $key => $language){
                $sql3 = "update `".DB_PREF.mysql_real_escape_string($this->translationTable)."`
        set `".mysql_real_escape_string($this->translationField)."` = AES_ENCRYPT('".mysql_real_escape_string($_REQUEST[$prefix.'_'.$language['id']])."', '".$this->secureKey."')
        where 
        `".mysql_real_escape_string($this->recordIdField)."` = '".(int)$rowId."' and `".mysql_real_escape_string($this->languageIdField)."` = '".(int)$language['id']." '
        ";
                $rs3 = mysql_query($sql3);
                if ($rs3){
                    if($stdModDb->updatedRowsCount() == 0){
                        $sql4 = "insert into `".DB_PREF.mysql_real_escape_string($this->translationTable)."`
              set `".mysql_real_escape_string($this->translationField)."` = AES_ENCRYPT('".mysql_real_escape_string($_REQUEST[$prefix.'_'.$language['id']])."', '".$this->secureKey."'),
              `".mysql_real_escape_string($this->languageIdField)."`  = '".(int)$language['id']."', `".mysql_real_escape_string($this->recordIdField)."` = '".$rowId."' ";
                        $rs4 = mysql_query($sql4);
                        if(!$rs4)
                        trigger_error($sql4." ".mysql_error());

                    }
                }else{
                    trigger_error("Can't update language field values ".$sql3." ".mysql_error());
                }
            }
        }
    }
    function processDelete($area, $id){
        $sql2 = "delete from `".DB_PREF.mysql_real_escape_string($this->translationTable)."` where `".$this->recordIdField."` = '".(int)$id."'";
        $rs2 = mysql_query($sql2);
        if(!$rs2)
        trigger_error("Can't delete language field values ".$sql2." ".mysql_error());
    }


    function printSearchField($level, $key, $area){
        if (isset($_GET['search'][$level][$key]))
        $value = $_GET['search'][$level][$key];
        else
        $value = '';
        return '<input name="search['.$level.']['.$key.']" value="'.htmlspecialchars($value).'" />';
    }

    function getFilterOption($value, $area){
        $answer = '';

        $sql = "select `".mysql_real_escape_string($this->recordIdField)."` as 'id' from `".DB_PREF.mysql_real_escape_string($this->translationTable)."`
    where AES_DECRYPT(".mysql_real_escape_string($this->translationField).", '".$this->secureKey."') like '%".mysql_real_escape_string($value)."%'";
        $rs = mysql_query($sql);
        if ($rs){
            $ids = array();
            while($lock = mysql_fetch_assoc($rs)){
                $ids[] = mysql_real_escape_string($lock['id']);
            }
        }
        if(sizeof($ids) > 0)
        $answer = ' `'.mysql_real_escape_string($area->dbPrimaryKey).'` in ('.implode($ids, ',').') ';
        else
        $answer = ' 0 ';

        return $answer;
    }




}